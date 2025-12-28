<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FraudIncident extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id', 'incident_type', 'severity', 'incident_data',
        'affected_users', 'action_taken', 'banned_until', 'resolved', 'resolution_notes'
    ];

    protected $casts = [
        'incident_data' => 'array',
        'affected_users' => 'array',
        'banned_until' => 'datetime',
        'resolved' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
