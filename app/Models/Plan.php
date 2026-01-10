<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name', 'display_name', 'description', 'order',
        'price', 'currency', 'features', 'badge_color',
        'icon', 'is_popular', 'is_active', 'rank_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'features' => 'array',
        'is_popular' => 'boolean',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    // Relationships
    public function rank() { return $this->belongsTo(Rank::class); }
    public function users() { return $this->hasMany(User::class); }
    public function subscriptions() { return $this->hasMany(UserSubscription::class); }
    public function activeSubscriptions() { return $this->hasMany(UserSubscription::class)->where('status', 'ACTIVE'); }

    // Scopes
    public function scopeActive($query) { return $query->where('is_active', true); }
    public function scopeOrdered($query) { return $query->orderBy('order'); }
    public function scopePopular($query) { return $query->where('is_popular', true); }

    // Helper Methods
    public function getMaxDailyTasks(): int { return $this->features['max_daily_tasks'] ?? 8; }
    public function getTaskRewardMultiplier(): float { return $this->features['task_reward_multiplier'] ?? 1.0; }
    public function hasPrioritySupport(): bool { return $this->features['priority_support'] ?? false; }
}
