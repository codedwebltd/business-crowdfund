<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id', 'pending_balance', 'withdrawable_balance', 'total_earned',
        'total_withdrawn', 'referral_balance', 'bonus_balance', 'locked_balance',
        'task_earnings_today', 'task_earnings_this_month', 'tasks_completed_today',
        'currency', 'last_transaction_at',
    ];

    protected $casts = [
        'pending_balance' => 'decimal:2',
        'withdrawable_balance' => 'decimal:2',
        'total_earned' => 'decimal:2',
        'total_withdrawn' => 'decimal:2',
        'referral_balance' => 'decimal:2',
        'bonus_balance' => 'decimal:2',
        'locked_balance' => 'decimal:2',
        'task_earnings_today' => 'decimal:2',
        'task_earnings_this_month' => 'decimal:2',
        'tasks_completed_today' => 'integer',
        'last_transaction_at' => 'datetime',
    ];

    // Relationships
    public function user() { return $this->belongsTo(User::class); }
    public function transactions() { return $this->hasMany(Transaction::class, 'user_id', 'user_id'); }

    // Helper Methods
    public function getTotalBalance(): float
    {
        return $this->pending_balance + $this->withdrawable_balance + $this->referral_balance + $this->bonus_balance;
    }

    public function canResetDaily(): bool
    {
        return $this->updated_at->isToday() === false;
    }

    public function resetDailyCounters(): void
    {
        $this->update([
            'task_earnings_today' => 0,
            'tasks_completed_today' => 0,
        ]);
    }
}
