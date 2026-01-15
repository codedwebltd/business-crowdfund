<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupportTicket extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'user_id',
        'guest_session_id',
        'guest_name',
        'guest_email',
        'subject',
        'first_message',
        'ticket_number',
        'status',
        'priority',
        'category',
        'is_read_by_admin',
        'is_read_by_user',
        'has_new_admin_reply',
        'has_new_user_reply',
        'last_message_at',
        'last_admin_reply_at',
        'last_user_reply_at',
        'resolved_by',
        'resolved_at',
        'resolution_note',
        'rating',
        'rating_comment',
        'ip_address',
        'user_agent',
        'page_url',
        'meta_data',
    ];

    protected $casts = [
        'is_read_by_admin' => 'boolean',
        'is_read_by_user' => 'boolean',
        'has_new_admin_reply' => 'boolean',
        'has_new_user_reply' => 'boolean',
        'last_message_at' => 'datetime',
        'last_admin_reply_at' => 'datetime',
        'last_user_reply_at' => 'datetime',
        'resolved_at' => 'datetime',
        'meta_data' => 'json',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            if (empty($ticket->ticket_number)) {
                $ticket->ticket_number = self::generateTicketNumber();
            }
        });
    }

    /**
     * Generate unique ticket number
     */
    public static function generateTicketNumber(): string
    {
        $year = date('Y');
        $lastTicket = self::withTrashed()
            ->whereYear('created_at', $year)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($lastTicket && preg_match('/TKT-' . $year . '-(\d+)/', $lastTicket->ticket_number, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }

        return sprintf('TKT-%s-%05d', $year, $nextNumber);
    }

    /**
     * Get the user who created the ticket.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who resolved the ticket.
     */
    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    /**
     * Get all messages for this ticket.
     */
    public function messages()
    {
        return $this->hasMany(SupportMessage::class, 'ticket_id')->orderBy('created_at', 'asc');
    }

    /**
     * Get the last message.
     */
    public function lastMessage()
    {
        return $this->hasOne(SupportMessage::class, 'ticket_id')->latest();
    }

    /**
     * Get the display name for the ticket owner
     */
    public function getOwnerNameAttribute(): string
    {
        if ($this->user) {
            return $this->user->full_name;
        }
        return $this->guest_name ?? 'Guest';
    }

    /**
     * Get the display email for the ticket owner
     */
    public function getOwnerEmailAttribute(): ?string
    {
        if ($this->user) {
            return $this->user->email;
        }
        return $this->guest_email;
    }

    /**
     * Scope: Open tickets
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    /**
     * Scope: In progress tickets
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope: Resolved tickets
     */
    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    /**
     * Scope: Unread by admin
     */
    public function scopeUnreadByAdmin($query)
    {
        return $query->where('is_read_by_admin', false);
    }

    /**
     * Scope: Has new user reply
     */
    public function scopeHasNewUserReply($query)
    {
        return $query->where('has_new_user_reply', true);
    }

    /**
     * Scope: For user (authenticated or guest)
     */
    public function scopeForUserOrGuest($query, $userId = null, $guestSessionId = null)
    {
        return $query->where(function ($q) use ($userId, $guestSessionId) {
            if ($userId) {
                $q->where('user_id', $userId);
            }
            if ($guestSessionId) {
                $q->orWhere('guest_session_id', $guestSessionId);
            }
        });
    }

    /**
     * Mark as read by admin
     */
    public function markAsReadByAdmin()
    {
        $this->update([
            'is_read_by_admin' => true,
            'has_new_user_reply' => false,
        ]);
        return $this;
    }

    /**
     * Mark as read by user
     */
    public function markAsReadByUser()
    {
        $this->update([
            'is_read_by_user' => true,
            'has_new_admin_reply' => false,
        ]);
        return $this;
    }

    /**
     * Resolve the ticket
     */
    public function resolve($adminId, $note = null)
    {
        $this->update([
            'status' => 'resolved',
            'resolved_by' => $adminId,
            'resolved_at' => now(),
            'resolution_note' => $note,
        ]);
        return $this;
    }

    /**
     * Reopen the ticket
     */
    public function reopen()
    {
        $this->update([
            'status' => 'open',
            'resolved_by' => null,
            'resolved_at' => null,
            'resolution_note' => null,
        ]);
        return $this;
    }

    /**
     * Close the ticket
     */
    public function close()
    {
        $this->update(['status' => 'closed']);
        return $this;
    }

    /**
     * Rate the support experience
     */
    public function rate($rating, $comment = null)
    {
        $this->update([
            'rating' => $rating,
            'rating_comment' => $comment,
        ]);
        return $this;
    }
}
