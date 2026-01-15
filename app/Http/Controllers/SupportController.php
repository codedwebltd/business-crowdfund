<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\SupportMessage;
use App\Models\User;
use App\Events\NewSupportTicket;
use App\Events\NewSupportMessage;
use App\Events\SupportTypingEvent;
use App\Services\FileUploadService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Inertia\Inertia;

class SupportController extends Controller
{
    protected FileUploadService $fileUploadService;
    protected NotificationService $notificationService;

    public function __construct(FileUploadService $fileUploadService, NotificationService $notificationService)
    {
        $this->fileUploadService = $fileUploadService;
        $this->notificationService = $notificationService;
    }

    /**
     * Show support page with ticket history
     */
    public function index()
    {
        $user = Auth::user();
        $guestSessionId = session('support_guest_id');

        $tickets = SupportTicket::with(['lastMessage'])
            ->forUserOrGuest($user?->id, $guestSessionId)
            ->orderBy('last_message_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Support/Index', [
            'tickets' => $tickets,
            'guestSessionId' => $guestSessionId,
        ]);
    }

    /**
     * Show a specific ticket conversation
     */
    public function show($ticketId)
    {
        $user = Auth::user();
        $guestSessionId = session('support_guest_id');

        $ticket = SupportTicket::with(['messages.sender', 'user'])
            ->where('id', $ticketId)
            ->forUserOrGuest($user?->id, $guestSessionId)
            ->firstOrFail();

        // Mark as read by user
        $ticket->markAsReadByUser();

        return Inertia::render('Support/Show', [
            'ticket' => $ticket,
            'messages' => $ticket->messages,
        ]);
    }

    /**
     * Start a new support ticket (API endpoint for widget)
     */
    public function startTicket(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|min:3',
            'subject' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:50',
            'guest_name' => 'nullable|string|max:255',
            'guest_email' => 'nullable|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        $guestSessionId = null;

        // If not authenticated, require email or use session
        if (!$user) {
            if (!$request->guest_email) {
                // Generate or retrieve guest session ID
                $guestSessionId = session('support_guest_id');
                if (!$guestSessionId) {
                    $guestSessionId = 'guest_' . Str::uuid();
                    session(['support_guest_id' => $guestSessionId]);
                }
            }
        }

        // Create the ticket
        $ticket = SupportTicket::create([
            'user_id' => $user?->id,
            'guest_session_id' => $guestSessionId,
            'guest_name' => $request->guest_name,
            'guest_email' => $request->guest_email,
            'subject' => $request->subject ?? 'Support Request',
            'first_message' => $request->message,
            'category' => $request->category ?? 'general',
            'status' => 'open',
            'priority' => 'medium',
            'last_message_at' => now(),
            'last_user_reply_at' => now(),
            'has_new_user_reply' => true,
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
            'page_url' => $request->header('Referer'),
            'meta_data' => [
                'browser_info' => $request->browser_info,
                'page_title' => $request->page_title,
            ],
        ]);

        // Create first message
        $message = SupportMessage::create([
            'ticket_id' => $ticket->id,
            'sender_type' => $user ? 'user' : 'guest',
            'sender_id' => $user?->id,
            'message' => $request->message,
            'message_type' => 'text',
            'is_read' => false,
        ]);

        // Broadcast new ticket to admin
        broadcast(new NewSupportTicket($ticket))->toOthers();
        broadcast(new NewSupportMessage($message))->toOthers();

        // Notify admins (you can customize this)
        $this->notifyAdmins('new_support_ticket', [
            'ticket_id' => $ticket->id,
            'ticket_number' => $ticket->ticket_number,
            'subject' => $ticket->subject,
            'from' => $ticket->owner_name,
        ]);

        // Store guest session if needed
        if ($guestSessionId) {
            session(['support_guest_id' => $guestSessionId]);
        }

        Log::info('New support ticket created', [
            'ticket_id' => $ticket->id,
            'ticket_number' => $ticket->ticket_number,
            'user_id' => $user?->id,
            'guest_session_id' => $guestSessionId,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Ticket created successfully',
            'ticket_id' => $ticket->id,
            'ticket_number' => $ticket->ticket_number,
            'guest_session_id' => $guestSessionId,
        ]);
    }

    /**
     * Send a message to a ticket (API endpoint)
     */
    public function sendMessage(Request $request, $ticketId)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required_without:file|string|nullable',
            'file' => 'nullable|file|max:51200', // 50MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        $guestSessionId = session('support_guest_id') ?? $request->guest_session_id;

        // Find the ticket and verify ownership
        $ticket = SupportTicket::where('id', $ticketId)
            ->forUserOrGuest($user?->id, $guestSessionId)
            ->first();

        if (!$ticket) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ticket not found or unauthorized'
            ], 404);
        }

        // Check if ticket is closed
        if ($ticket->status === 'closed') {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot send messages to closed tickets'
            ], 400);
        }

        // Handle file upload
        $filePath = null;
        $fileName = null;
        $fileType = null;
        $fileSize = null;
        $messageType = 'text';

        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $file = $request->file('file');
            $fileName = $file->getClientOriginalName();
            $mimeType = $file->getMimeType();

            // Determine message type
            if (str_starts_with($mimeType, 'image/')) {
                $messageType = 'image';
            } else {
                $messageType = 'file';
            }

            // Upload to Backblaze
            $directory = 'support/attachments/' . $ticket->id;
            $result = $this->fileUploadService->uploadFile($file, $directory, $user?->id ?? $guestSessionId);

            if ($result['success']) {
                $filePath = $result['url'];
                $fileSize = $result['size'];
                $fileType = $result['type'];
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'File upload failed'
                ], 500);
            }
        }

        // Create message
        $message = SupportMessage::create([
            'ticket_id' => $ticket->id,
            'sender_type' => $user ? 'user' : 'guest',
            'sender_id' => $user?->id,
            'message' => $request->message ?? '',
            'message_type' => $messageType,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_type' => $fileType,
            'file_size' => $fileSize,
            'is_read' => false,
        ]);

        // Update ticket
        $ticket->update([
            'last_message_at' => now(),
            'last_user_reply_at' => now(),
            'has_new_user_reply' => true,
            'is_read_by_admin' => false,
            'status' => $ticket->status === 'resolved' ? 'open' : $ticket->status, // Reopen if was resolved
        ]);

        // Broadcast message
        broadcast(new NewSupportMessage($message))->toOthers();

        // Notify admins of new message for immediate response
        $this->notifyAdmins('new_support_message', [
            'ticket_id' => $ticket->id,
            'ticket_number' => $ticket->ticket_number,
            'subject' => $ticket->subject,
            'message_preview' => Str::limit($request->message ?? '[File attached]', 100),
            'from' => $ticket->owner_name,
            'is_new_ticket' => false,
        ]);

        Log::info('Support message sent', [
            'ticket_id' => $ticket->id,
            'message_id' => $message->id,
            'sender_type' => $message->sender_type,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Message sent successfully',
            'data' => $message,
        ]);
    }

    /**
     * Get messages for a ticket (API endpoint for widget)
     */
    public function getMessages(Request $request, $ticketId)
    {
        $user = Auth::user();
        $guestSessionId = session('support_guest_id') ?? $request->guest_session_id;

        $ticket = SupportTicket::where('id', $ticketId)
            ->forUserOrGuest($user?->id, $guestSessionId)
            ->first();

        if (!$ticket) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ticket not found or unauthorized'
            ], 404);
        }

        $query = $ticket->messages()->with('sender')->orderBy('created_at', 'asc');

        // If since_id provided, only get new messages
        if ($request->has('since_id')) {
            $query->where('id', '>', $request->since_id);
        }

        $messages = $query->get();

        // Mark admin messages as read
        $ticket->messages()
            ->where('sender_type', 'admin')
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        // Update ticket read status
        $ticket->markAsReadByUser();

        return response()->json([
            'status' => 'success',
            'messages' => $messages,
            'ticket_status' => $ticket->status,
        ]);
    }

    /**
     * Get user's ticket history (API endpoint)
     */
    public function getTickets(Request $request)
    {
        $user = Auth::user();
        $guestSessionId = session('support_guest_id') ?? $request->guest_session_id;

        $tickets = SupportTicket::with(['lastMessage'])
            ->forUserOrGuest($user?->id, $guestSessionId)
            ->orderBy('last_message_at', 'desc')
            ->get()
            ->map(function ($ticket) {
                return [
                    'id' => $ticket->id,
                    'ticket_number' => $ticket->ticket_number,
                    'subject' => $ticket->subject,
                    'status' => $ticket->status,
                    'has_new_admin_reply' => $ticket->has_new_admin_reply,
                    'last_message' => $ticket->lastMessage?->message,
                    'last_message_at' => $ticket->last_message_at?->diffForHumans(),
                    'created_at' => $ticket->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'status' => 'success',
            'tickets' => $tickets,
        ]);
    }

    /**
     * Get or resume existing conversation (API endpoint for widget)
     */
    public function getOrCreateTicket(Request $request)
    {
        $user = Auth::user();
        $guestSessionId = session('support_guest_id') ?? $request->guest_session_id;

        // Try to find an open ticket
        $ticket = SupportTicket::with(['messages.sender'])
            ->forUserOrGuest($user?->id, $guestSessionId)
            ->whereIn('status', ['open', 'in_progress', 'awaiting_reply'])
            ->orderBy('last_message_at', 'desc')
            ->first();

        if ($ticket) {
            $ticket->markAsReadByUser();

            return response()->json([
                'status' => 'success',
                'has_ticket' => true,
                'ticket' => [
                    'id' => $ticket->id,
                    'ticket_number' => $ticket->ticket_number,
                    'subject' => $ticket->subject,
                    'status' => $ticket->status,
                ],
                'messages' => $ticket->messages,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'has_ticket' => false,
            'guest_session_id' => $guestSessionId,
        ]);
    }

    /**
     * Typing indicator (API endpoint)
     */
    public function typing(Request $request, $ticketId)
    {
        $validator = Validator::make($request->all(), [
            'is_typing' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        $guestSessionId = session('support_guest_id') ?? $request->guest_session_id;

        // Verify ticket ownership
        $ticket = SupportTicket::where('id', $ticketId)
            ->forUserOrGuest($user?->id, $guestSessionId)
            ->first();

        if (!$ticket) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ticket not found'
            ], 404);
        }

        // Broadcast typing event
        broadcast(new SupportTypingEvent(
            $ticketId,
            $request->is_typing,
            $user ? 'user' : 'guest',
            $user?->id ?? $guestSessionId,
            $user?->full_name ?? $ticket->guest_name ?? 'Guest'
        ))->toOthers();

        return response()->json(['status' => 'success']);
    }

    /**
     * Close ticket from user side
     */
    public function closeTicket(Request $request, $ticketId)
    {
        $user = Auth::user();
        $guestSessionId = session('support_guest_id') ?? $request->guest_session_id;

        $ticket = SupportTicket::where('id', $ticketId)
            ->forUserOrGuest($user?->id, $guestSessionId)
            ->first();

        if (!$ticket) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ticket not found'
            ], 404);
        }

        $ticket->close();

        // Add system message
        SupportMessage::createSystemMessage(
            $ticket->id,
            'Ticket closed by ' . ($user?->full_name ?? 'user')
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Ticket closed successfully'
        ]);
    }

    /**
     * Rate support experience
     */
    public function rateTicket(Request $request, $ticketId)
    {
        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        $guestSessionId = session('support_guest_id') ?? $request->guest_session_id;

        $ticket = SupportTicket::where('id', $ticketId)
            ->forUserOrGuest($user?->id, $guestSessionId)
            ->first();

        if (!$ticket) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ticket not found'
            ], 404);
        }

        $ticket->rate($request->rating, $request->comment);

        // Add system message
        SupportMessage::createSystemMessage(
            $ticket->id,
            'Rated ' . $request->rating . ' stars'
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Thank you for your feedback!'
        ]);
    }

    /**
     * Notify admin users of new support activity
     */
    protected function notifyAdmins(string $type, array $data): void
    {
        try {
            $admins = User::where('role', 1)->get(); // role = 1 is admin

            foreach ($admins as $admin) {
                $this->notificationService->send($admin, $type, $data);
            }
        } catch (\Exception $e) {
            Log::error('Failed to notify admins', [
                'type' => $type,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
