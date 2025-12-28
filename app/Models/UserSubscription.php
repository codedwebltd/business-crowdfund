<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserSubscription extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'user_id', 'plan_id', 'amount_paid', 'currency', 'payment_method',
        'payment_reference', 'transaction_id', 'status', 'payment_verified_at',
        'activated_at', 'verified_by', 'admin_notes',
        'payment_proof', 'is_upgrade', 'upgraded_from_subscription_id',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'payment_proof' => 'array',
        'is_upgrade' => 'boolean',
        'payment_verified_at' => 'datetime',
        'activated_at' => 'datetime',
    ];

    // Relationships
    public function user() { return $this->belongsTo(User::class); }
    public function plan() { return $this->belongsTo(Plan::class); }
    public function verifiedBy() { return $this->belongsTo(User::class, 'verified_by'); }
    public function upgradedFrom() { return $this->belongsTo(UserSubscription::class, 'upgraded_from_subscription_id'); }

    // Scopes
    public function scopeActive($query) { return $query->where('status', 'ACTIVE'); }
    public function scopePending($query) { return $query->where('status', 'PENDING'); }
    public function scopeVerified($query) { return $query->where('status', 'VERIFIED'); }
    public function scopeByPaymentMethod($query, $method) { return $query->where('payment_method', $method); }

    // Helper Methods
    public function isActive(): bool { return $this->status === 'ACTIVE'; }
    public function isPending(): bool { return $this->status === 'PENDING'; }
    public function isVerified(): bool { return $this->status === 'VERIFIED'; }
    public function isRejected(): bool { return $this->status === 'REJECTED'; }
}
