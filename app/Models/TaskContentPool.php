<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskContentPool extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'task_content_pool';

    protected $fillable = [
        'type',
        'title',
        'content',
        'source',
        'times_used',
        'last_used_at',
        'is_active',
    ];

    protected $casts = [
        'content' => 'array',
        'last_used_at' => 'datetime',
        'is_active' => 'boolean',
        'times_used' => 'integer',
    ];

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeUnused($query)
    {
        return $query->where('times_used', 0);
    }

    public function scopeNotUsedRecently($query, $days = 30)
    {
        return $query->where(function($q) use ($days) {
            $q->whereNull('last_used_at')
              ->orWhere('last_used_at', '<', now()->subDays($days));
        });
    }

    /**
     * Mark this content as used
     */
    public function markAsUsed()
    {
        $this->increment('times_used');
        $this->update(['last_used_at' => now()]);
    }
}
