<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'message', 'type', 'priority', 'is_active', 'is_dismissable',
        'target_audience', 'start_date', 'end_date', 'link_url', 'link_text',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_dismissable' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'priority' => 'integer',
    ];

    // Relationships
    public function dismissedByUsers() { return $this->belongsToMany(User::class, 'announcement_users')->withTimestamps(); }

    // Scopes
    public function scopeActive($query) {
        return $query->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            });
    }

    public function scopeForUser($query, User $user) {
        return $query->where(function($q) use ($user) {
            $q->where('target_audience', 'all')
              ->orWhere('target_audience', strtolower($user->status));
        });
    }

    public function scopeNotDismissedBy($query, User $user) {
        return $query->whereDoesntHave('dismissedByUsers', function($q) use ($user) {
            $q->where('user_id', $user->id);
        });
    }

    public function scopeOrdered($query) { return $query->orderBy('priority', 'desc')->orderBy('created_at', 'desc'); }
}
