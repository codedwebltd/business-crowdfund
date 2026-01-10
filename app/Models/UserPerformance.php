<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPerformance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tasks_completed_this_week',
        'referrals_this_week',
        'total_referrals',
        'direct_referrals',
        'team_size',
        'referral_depth',
        'star_rating',
        'priority_level',
        'last_calculated_at',
        'last_active_at',
    ];

    protected $casts = [
        'last_calculated_at' => 'datetime',
        'last_active_at' => 'datetime',
    ];

    /**
     * Get the user that owns the performance record.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get star display with emoji
     */
    public function getStarDisplayAttribute()
    {
        return str_repeat('⭐', $this->star_rating);
    }

    /**
     * Get priority display with badge color
     */
    public function getPriorityBadgeAttribute()
    {
        return match($this->priority_level) {
            5 => ['color' => 'red', 'text' => 'Urgent', 'icon' => '🔴'],
            4 => ['color' => 'orange', 'text' => 'High', 'icon' => '🟠'],
            3 => ['color' => 'yellow', 'text' => 'Medium', 'icon' => '🟡'],
            2 => ['color' => 'blue', 'text' => 'Low', 'icon' => '🔵'],
            default => ['color' => 'gray', 'text' => 'Very Low', 'icon' => '⚪'],
        };
    }

    /**
     * Check if user is a "General" (5-star)
     */
    public function isGeneral()
    {
        return $this->star_rating === 5;
    }
}
