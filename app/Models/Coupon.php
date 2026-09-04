<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'code',
        'type',
        'value',
        'starts_at',
        'expires_at',
        'usage_limit',
        'used_count',
        'min_subtotal_cents',
        'is_active',
    ];

    protected $casts = [
        'value' => 'integer',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
        'min_subtotal_cents' => 'integer',
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function isValid(?int $subtotalCents = null): bool
    {
        if (!$this->is_active) {
            return false;
        }
        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }
        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return false;
        }
        if ($subtotalCents !== null && $subtotalCents < $this->min_subtotal_cents) {
            return false;
        }
        return true;
    }

    public function calculateDiscount(int $subtotalCents): int
    {
        if ($this->type === 'percent') {
            return (int) round($subtotalCents * ($this->value / 100));
        }
        return min($this->value, $subtotalCents);
    }
}