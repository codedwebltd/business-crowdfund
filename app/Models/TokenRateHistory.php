<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TokenRateHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'token_price', 'withdrawal_rate', 'price_change', 'rate_change',
        'trend', 'changed_by', 'change_reason',
    ];

    protected $casts = [
        'token_price' => 'decimal:2',
        'withdrawal_rate' => 'decimal:4',
        'price_change' => 'decimal:2',
        'rate_change' => 'decimal:4',
    ];

    // Relationships
    public function admin() { return $this->belongsTo(User::class, 'changed_by'); }

    // Helper: Get trend with percentage
    public function getTrendPercentage(): string
    {
        if (!$this->rate_change) return '0%';
        $percentage = abs($this->rate_change * 100);
        return number_format($percentage, 2) . '%';
    }

    // Helper: Is good time to withdraw? (rate above threshold)
    public function isGoodWithdrawalTime(float $threshold = 0.70): bool
    {
        return $this->withdrawal_rate >= $threshold;
    }
}
