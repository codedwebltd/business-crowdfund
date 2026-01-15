<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\SupportMessage;
use App\Models\User;
use App\Events\NewSupportMessage;
use App\Events\SupportTypingEvent;
use App\Services\FileUploadService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
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
     * Display support tickets list
     */
    public function index(Request $request)
    {
        $status = $request->status ?? 'open';
        $search = $request->search;
        $category = $request->category;

        $query = SupportTicket::with(['user', 'lastMessage', 'resolver']);

        // Filter by status
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        // Filter by category
        if ($category) {
            $query->where('category', $category);
        }

        // Search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('first_message', 'like', "%{$search}%")
                    ->orWhere('guest_name', 'like', "%{$search}%")
                    ->orWhere('guest_email', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q2) use ($search) {
                        $q2->where('full_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $tickets = $query->orderBy('has_new_user_reply', 'desc')
            ->orderBy('last_message_at', 'desc')
            ->paginate(20);

        // Get counts for tabs
        $counts = [
            'open' => SupportTicket::where('status', 'open')->count(),
            'in_progress' => SupportTicket::where('status', 'in_progress')->count(),
            'awaiting_reply' => SupportTicket::where('status', 'awaiting_reply')->count(),
            'resolved' => SupportTicket::where('status', 'resolved')->count(),
            'closed' => SupportTicket::where('status', 'closed')->count(),
            'unread' => SupportTicket::where('has_new_user_reply', true)->count(),
        ];

        return Inertia::render('Admin/Support/Index', [
            'tickets' => $tickets,
            'counts' => $counts,
            'filters' => [
                'status' => $status,
                'search' => $search,
                'category' => $category,
            ],
        ]);
    }

    /**
     * Show a specific ticket conversation
     */
    public function show($ticketId)
    {
        $ticket = SupportTicket::with(['messages.sender', 'user', 'resolver'])
            ->findOrFail($ticketId);

        // Mark as read by admin
        $ticket->markAsReadByAdmin();

        return Inertia::render('Admin/Support/Show', [
            'ticket' => $ticket,
            'messages' => $ticket->messages,
        ]);
    }

    /**
     * Send message from admin (API)
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

        $admin = Auth::user();
        $ticket = SupportTicket::findOrFail($ticketId);

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
            $result = $this->fileUploadService->uploadFile($file, $directory, $admin->id);

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
            'sender_type' => 'admin',
            'sender_id' => $admin->id,
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
            'last_admin_reply_at' => now(),
            'has_new_admin_reply' => true,
            'is_read_by_user' => false,
            'has_new_user_reply' => false,
            'status' => $ticket->status === 'open' ? 'in_progress' : $ticket->status,
        ]);

        // Broadcast message
        broadcast(new NewSupportMessage($message))->toOthers();

        // Notify user if they're authenticated
        if ($ticket->user) {
            $this->notificationService->send($ticket->user, 'support_reply', [
                'ticket_id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'subject' => $ticket->subject,
                'message_preview' => \Str::limit($message->message, 100),
            ]);
        }

        Log::info('Admin sent support message', [
            'ticket_id' => $ticket->id,
            'message_id' => $message->id,
            'admin_id' => $admin->id,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Message sent successfully',
            'data' => $message->load('sender'),
        ]);
    }

    /**
     * Get messages for a ticket (API)
     */
    public function getMessages(Request $request, $ticketId)
    {
        $ticket = SupportTicket::findOrFail($ticketId);

        $query = $ticket->messages()->with('sender')->orderBy('created_at', 'asc');

        // If since_id provided, only get new messages
        if ($request->has('since_id')) {
            $query->where('id', '>', $request->since_id);
        }

        $messages = $query->get();

        // Mark user messages as read
        $ticket->messages()
            ->whereIn('sender_type', ['user', 'guest'])
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        $ticket->markAsReadByAdmin();

        return response()->json([
            'status' => 'success',
            'messages' => $messages,
            'ticket' => $ticket->fresh(),
        ]);
    }

    /**
     * Admin typing indicator (API)
     */
    public function typing(Request $request, $ticketId)
    {
        $validator = Validator::make($request->all(), [
            'is_typing' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error'], 422);
        }

        $admin = Auth::user();
        $ticket = SupportTicket::findOrFail($ticketId);

        // Broadcast typing event
        broadcast(new SupportTypingEvent(
            $ticketId,
            $request->is_typing,
            'admin',
            $admin->id,
            $admin->full_name ?? 'Support Agent'
        ))->toOthers();

        return response()->json(['status' => 'success']);
    }

    /**
     * Update ticket status
     */
    public function updateStatus(Request $request, $ticketId)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:open,in_progress,awaiting_reply,resolved,closed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid status',
                'errors' => $validator->errors()
            ], 422);
        }

        $admin = Auth::user();
        $ticket = SupportTicket::findOrFail($ticketId);
        $oldStatus = $ticket->status;

        $updateData = ['status' => $request->status];

        // If resolving, set resolver
        if ($request->status === 'resolved' && $oldStatus !== 'resolved') {
            $updateData['resolved_by'] = $admin->id;
            $updateData['resolved_at'] = now();
            $updateData['resolution_note'] = $request->resolution_note;
        }

        // If reopening, clear resolution
        if (in_array($request->status, ['open', 'in_progress']) && $oldStatus === 'resolved') {
            $updateData['resolved_by'] = null;
            $updateData['resolved_at'] = null;
            $updateData['resolution_note'] = null;
        }

        $ticket->update($updateData);

        // Add system message
        SupportMessage::createSystemMessage(
            $ticket->id,
            'Status changed from ' . ucfirst($oldStatus) . ' to ' . ucfirst($request->status) . ' by ' . $admin->full_name
        );

        // Broadcast update
        broadcast(new NewSupportMessage(
            $ticket->messages()->latest()->first()
        ))->toOthers();

        return response()->json([
            'status' => 'success',
            'message' => 'Status updated successfully',
            'ticket' => $ticket->fresh(),
        ]);
    }

    /**
     * Update ticket priority
     */
    public function updatePriority(Request $request, $ticketId)
    {
        $validator = Validator::make($request->all(), [
            'priority' => 'required|in:low,medium,high,urgent',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid priority'
            ], 422);
        }

        $ticket = SupportTicket::findOrFail($ticketId);
        $ticket->update(['priority' => $request->priority]);

        return response()->json([
            'status' => 'success',
            'message' => 'Priority updated successfully',
            'ticket' => $ticket->fresh(),
        ]);
    }

    /**
     * Assign ticket to admin
     */
    public function assignTicket(Request $request, $ticketId)
    {
        $ticket = SupportTicket::findOrFail($ticketId);

        // For now, just mark as in_progress by current admin
        $admin = Auth::user();
        $ticket->update([
            'status' => 'in_progress',
        ]);

        SupportMessage::createSystemMessage(
            $ticket->id,
            'Ticket assigned to ' . $admin->full_name
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Ticket assigned successfully',
        ]);
    }

    /**
     * Get support statistics
     */
    public function stats()
    {
        $today = now()->startOfDay();
        $thisWeek = now()->startOfWeek();
        $thisMonth = now()->startOfMonth();

        return response()->json([
            'total_tickets' => SupportTicket::count(),
            'open_tickets' => SupportTicket::whereIn('status', ['open', 'in_progress', 'awaiting_reply'])->count(),
            'resolved_today' => SupportTicket::where('resolved_at', '>=', $today)->count(),
            'new_today' => SupportTicket::where('created_at', '>=', $today)->count(),
            'new_this_week' => SupportTicket::where('created_at', '>=', $thisWeek)->count(),
            'average_rating' => SupportTicket::whereNotNull('rating')->avg('rating'),
            'unread_count' => SupportTicket::where('has_new_user_reply', true)->count(),
        ]);
    }
}
