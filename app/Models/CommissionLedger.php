<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommissionLedger extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'source_user_id',
        'source_task_id',
        'amount',
        'level',
        'commission_rate',
        'status',
        'batch_id',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'level' => 'integer',
        'processed_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sourceUser()
    {
        return $this->belongsTo(User::class, 'source_user_id');
    }

    public function sourceTask()
    {
        return $this->belongsTo(UserTask::class, 'source_task_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'PENDING');
    }

    public function scopeProcessed($query)
    {
        return $query->where('status', 'PROCESSED');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
