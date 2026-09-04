<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'seller_id',
        'product_title',
        'variant_name',
        'unit_price_cents',
        'quantity',
        'discount_cents',
        'total_cents',
        'platform_fee_cents',
        'seller_earnings_cents',
        'license_key',
    ];

    protected $casts = [
        'unit_price_cents' => 'integer',
        'quantity' => 'integer',
        'discount_cents' => 'integer',
        'total_cents' => 'integer',
        'platform_fee_cents' => 'integer',
        'seller_earnings_cents' => 'integer',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function downloads(): HasMany
    {
        return $this->hasMany(Download::class);
    }

    public function review(): HasMany
    {
        return $this->hasOne(Review::class);
    }

    public function hasDownloadAccess(): bool
    {
        return $this->order->isPaid();
    }
}