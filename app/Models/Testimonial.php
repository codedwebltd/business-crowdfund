<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id', 'name', 'message', 'status',
        'ai_corrected_message', 'ai_analysis', 'auto_approved',
        'trash_testimonial', 'negative_testimonial', 'complaint_testimonial',
        'ai_processed_at', 'admin_notes', 'reviewed_at', 'reviewed_by'
    ];

    protected $casts = [
        'ai_analysis' => 'array',
        'auto_approved' => 'boolean',
        'trash_testimonial' => 'boolean',
        'negative_testimonial' => 'boolean',
        'complaint_testimonial' => 'boolean',
        'ai_processed_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
