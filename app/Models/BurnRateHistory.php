<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BurnRateHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_date',
        'total_deposits',
        'total_withdrawals',
        'pending_withdrawals',
        'platform_balance',
        'burn_rate',
        'liquidity_status',
        'consecutive_critical_days',
        'active_users_count',
        'new_activations_count',
        'withdrawal_requests_count',
        'thresholds_snapshot',
        'metadata',
        'admin_alerted',
        'alerted_at',
    ];

    protected $casts = [
        'report_date' => 'date',
        'total_deposits' => 'decimal:2',
        'total_withdrawals' => 'decimal:2',
        'pending_withdrawals' => 'decimal:2',
        'platform_balance' => 'decimal:2',
        'burn_rate' => 'decimal:4',
        'consecutive_critical_days' => 'integer',
        'active_users_count' => 'integer',
        'new_activations_count' => 'integer',
        'withdrawal_requests_count' => 'integer',
        'thresholds_snapshot' => 'array',
        'metadata' => 'array',
        'admin_alerted' => 'boolean',
        'alerted_at' => 'datetime',
    ];

    /**
     * Get burn rate status color for UI
     */
    public function getStatusColorAttribute()
    {
        return match($this->liquidity_status) {
            'healthy' => 'green',
            'caution' => 'yellow',
            'critical' => 'orange',
            'collapse_imminent' => 'red',
            default => 'gray',
        };
    }

    /**
     * Scope: Get recent history
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('report_date', '>=', now()->subDays($days))
                    ->orderBy('report_date', 'desc');
    }

    /**
     * Scope: Get critical days only
     */
    public function scopeCritical($query)
    {
        return $query->whereIn('liquidity_status', ['critical', 'collapse_imminent']);
    }
}
