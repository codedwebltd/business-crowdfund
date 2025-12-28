<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id', 'transaction_type', 'balance_type', 'status', 'amount',
        'currency', 'is_credit', 'description', 'reference_id', 'reference_type',
        'processed_by', 'processed_at', 'metadata', 'transaction_hash',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_credit' => 'boolean',
        'metadata' => 'array',
        'processed_at' => 'datetime',
    ];

    // Relationships
    public function user() { return $this->belongsTo(User::class); }
    public function processedBy() { return $this->belongsTo(User::class, 'processed_by'); }
    public function reference() { return $this->morphTo(); }

    // Scopes
    public function scopeCredit($query) { return $query->where('is_credit', true); }
    public function scopeDebit($query) { return $query->where('is_credit', false); }
    public function scopePending($query) { return $query->where('status', 'PENDING'); }
    public function scopeCompleted($query) { return $query->where('status', 'COMPLETED'); }
    public function scopeByType($query, $type) { return $query->where('transaction_type', $type); }

    // Helper Methods
    public function isPending(): bool { return $this->status === 'PENDING'; }
    public function isCompleted(): bool { return $this->status === 'COMPLETED'; }
    public function isCredit(): bool { return $this->is_credit; }
}
