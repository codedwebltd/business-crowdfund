<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTask extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id', 'task_template_id', 'status', 'assigned_at', 'started_at',
        'completed_at', 'expires_at', 'response_data', 'completion_duration_seconds',
        'reward_amount', 'credited', 'transaction_id', 'validation_score',
        'validation_notes', 'ip_address', 'user_agent', 'device_fingerprint',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'expires_at' => 'datetime',
        'response_data' => 'array',
        'completion_duration_seconds' => 'integer',
        'reward_amount' => 'decimal:2',
        'credited' => 'boolean',
        'validation_score' => 'integer',
        'device_fingerprint' => 'array',
    ];

    // Relationships
    public function user() { return $this->belongsTo(User::class); }
    public function taskTemplate() { return $this->belongsTo(TaskTemplate::class); }
    public function transaction() { return $this->belongsTo(Transaction::class); }

    // Scopes
    public function scopePending($query) { return $query->where('status', 'PENDING'); }
    public function scopeInProgress($query) { return $query->where('status', 'IN_PROGRESS'); }
    public function scopeCompleted($query) { return $query->where('status', 'COMPLETED'); }
    public function scopeExpired($query) { return $query->where('status', 'EXPIRED'); }
    public function scopeCredited($query) { return $query->where('credited', true); }
    public function scopeToday($query) { return $query->whereDate('assigned_at', today()); }

    // Helper Methods
    public function isPending(): bool { return $this->status === 'PENDING'; }
    public function isInProgress(): bool { return $this->status === 'IN_PROGRESS'; }
    public function isCompleted(): bool { return $this->status === 'COMPLETED'; }
    public function isExpired(): bool { return $this->status === 'EXPIRED' || ($this->expires_at && $this->expires_at->isPast()); }
    public function isCredited(): bool { return $this->credited; }

    public function canStart(): bool
    {
        return $this->isPending() && !$this->isExpired();
    }

    public function canComplete(): bool
    {
        return $this->isInProgress() && !$this->isExpired();
    }

    public function markAsStarted(): void
    {
        $this->update([
            'status' => 'IN_PROGRESS',
            'started_at' => now(),
        ]);
    }

    public function markAsCompleted(array $responseData, int $duration): void
    {
        $this->update([
            'status' => 'COMPLETED',
            'completed_at' => now(),
            'response_data' => $responseData,
            'completion_duration_seconds' => $duration,
        ]);
    }

    public function markAsExpired(): void
    {
        $this->update(['status' => 'EXPIRED']);
    }
}
