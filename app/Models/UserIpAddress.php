<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserIpAddress extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id', 'ip_address', 'ip_type', 'is_vpn', 'is_proxy', 'is_tor',
        'is_datacenter', 'threat_level', 'country_code', 'city', 'isp',
        'organization', 'detection_data', 'usage_type', 'first_seen_at',
        'last_seen_at', 'usage_count'
    ];

    protected $casts = [
        'is_vpn' => 'boolean',
        'is_proxy' => 'boolean',
        'is_tor' => 'boolean',
        'is_datacenter' => 'boolean',
        'detection_data' => 'array',
        'first_seen_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'usage_count' => 'integer',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Helper methods
    public function isSuspicious(): bool
    {
        return $this->is_vpn || $this->is_proxy || $this->is_tor || $this->is_datacenter;
    }

    public function isHighRisk(): bool
    {
        return in_array($this->threat_level, ['HIGH', 'CRITICAL']);
    }

    // Scopes
    public function scopeVpnOrProxy($query)
    {
        return $query->where(function($q) {
            $q->where('is_vpn', true)
              ->orWhere('is_proxy', true)
              ->orWhere('is_tor', true);
        });
    }

    public function scopeHighRisk($query)
    {
        return $query->whereIn('threat_level', ['HIGH', 'CRITICAL']);
    }

    public function scopeRegistrations($query)
    {
        return $query->where('usage_type', 'REGISTRATION');
    }
}
