<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupportMessage extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'ticket_id',
        'sender_type',
        'sender_id',
        'message',
        'message_type',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'is_read',
        'read_at',
        'meta_data',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'meta_data' => 'json',
    ];

    /**
     * Get the ticket this message belongs to.
     */
    public function ticket()
    {
        return $this->belongsTo(SupportTicket::class, 'ticket_id');
    }

    /**
     * Get the sender (user/admin).
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get sender display name
     */
    public function getSenderNameAttribute(): string
    {
        if ($this->sender) {
            return $this->sender->full_name;
        }

        if ($this->sender_type === 'guest') {
            return $this->ticket?->guest_name ?? 'Guest';
        }

        if ($this->sender_type === 'system') {
            return 'System';
        }

        return 'Unknown';
    }

    /**
     * Check if message is from admin
     */
    public function isFromAdmin(): bool
    {
        return $this->sender_type === 'admin';
    }

    /**
     * Check if message is from user/guest
     */
    public function isFromUser(): bool
    {
        return in_array($this->sender_type, ['user', 'guest']);
    }

    /**
     * Check if message is system message
     */
    public function isSystem(): bool
    {
        return $this->sender_type === 'system';
    }

    /**
     * Check if has attachment
     */
    public function hasAttachment(): bool
    {
        return !empty($this->file_path);
    }

    /**
     * Scope: From admin
     */
    public function scopeFromAdmin($query)
    {
        return $query->where('sender_type', 'admin');
    }

    /**
     * Scope: From user
     */
    public function scopeFromUser($query)
    {
        return $query->whereIn('sender_type', ['user', 'guest']);
    }

    /**
     * Scope: Unread
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Mark as read
     */
    public function markAsRead()
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
        return $this;
    }

    /**
     * Create a system message
     */
    public static function createSystemMessage($ticketId, $message)
    {
        return self::create([
            'ticket_id' => $ticketId,
            'sender_type' => 'system',
            'message' => $message,
            'message_type' => 'system',
            'is_read' => true,
        ]);
    }
}
