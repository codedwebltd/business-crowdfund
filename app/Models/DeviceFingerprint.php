<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceFingerprint extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'fingerprint_hash',
        'fingerprint_data',
        'user_agent',
        'ip_address',
        'usage_count',
        'first_seen_at',
        'last_seen_at',
    ];

    protected $casts = [
        'fingerprint_data' => 'array',
        'first_seen_at' => 'datetime',
        'last_seen_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all users using this device
     */
    public function users()
    {
        return User::where('id', '!=', $this->user_id)
            ->whereHas('deviceFingerprints', function ($query) {
                $query->where('fingerprint_hash', $this->fingerprint_hash);
            });
    }

    /**
     * Check if this device is used by multiple accounts
     */
    public function isSharedDevice()
    {
        return DeviceFingerprint::where('fingerprint_hash', $this->fingerprint_hash)
            ->distinct('user_id')
            ->count() > 1;
    }
}
