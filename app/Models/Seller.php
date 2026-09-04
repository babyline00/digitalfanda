<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Seller extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'store_name',
        'slug',
        'bio',
        'logo_path',
        'banner_path',
        'website',
        'status',
        'commission_rate',
        'payout_email',
        'payout_method',
        'payout_details',
        'kyc_status',
        'balance_cents',
        'total_sales_cents',
        'approved_at',
        'suspended_at',
    ];

    protected $casts = [
        'commission_rate' => 'decimal:2',
        'payout_details' => 'array',
        'balance_cents' => 'integer',
        'total_sales_cents' => 'integer',
        'approved_at' => 'datetime',
        'suspended_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function orders(): HasMany
    {
        return $this->hasManyThrough(Order::class, Product::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payouts(): HasMany
    {
        return $this->hasMany(Payout::class);
    }

    public function coupons(): HasMany
    {
        return $this->hasMany(Coupon::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function getEffectiveCommissionRate(): float
    {
        return $this->commission_rate ?? (float) setting('commissions.platform_commission_rate', 10.0);
    }
}