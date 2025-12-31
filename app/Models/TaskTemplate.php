<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskTemplate extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'category', 'title', 'description', 'questions', 'validation_rules', 'data',
        'reward_amount', 'currency', 'completion_time_seconds', 'min_completion_time',
        'max_completion_time', 'priority', 'is_active', 'is_featured',
        'max_completions', 'current_completions', 'min_rank_id',
        'video_url', 'video_duration_seconds', 'instructions',
    ];

    protected $casts = [
        'questions' => 'array',
        'validation_rules' => 'array',
        'data' => 'array',
        'reward_amount' => 'decimal:2',
        'completion_time_seconds' => 'integer',
        'min_completion_time' => 'integer',
        'max_completion_time' => 'integer',
        'priority' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'max_completions' => 'integer',
        'current_completions' => 'integer',
        'video_duration_seconds' => 'integer',
    ];

    // Relationships
    public function minRank() { return $this->belongsTo(Rank::class, 'min_rank_id'); }
    public function userTasks() { return $this->hasMany(UserTask::class); }

    // Scopes
    public function scopeActive($query) { return $query->where('is_active', true); }
    public function scopeFeatured($query) { return $query->where('is_featured', true); }
    public function scopeByCategory($query, $category) { return $query->where('category', $category); }
    public function scopeOrdered($query) { return $query->orderBy('priority', 'desc'); }
    public function scopeAvailable($query) { return $query->where('is_active', true)->whereRaw('(max_completions IS NULL OR current_completions < max_completions)'); }

    // Helper Methods
    public function isAvailable(): bool
    {
        return $this->is_active && (is_null($this->max_completions) || $this->current_completions < $this->max_completions);
    }

    public function remainingSlots(): ?int
    {
        if (is_null($this->max_completions)) return null;
        return max(0, $this->max_completions - $this->current_completions);
    }

    public function incrementCompletions(): void
    {
        $this->increment('current_completions');
    }
}
