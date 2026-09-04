<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'seller_id',
        'category_id',
        'title',
        'slug',
        'subtitle',
        'description',
        'type',
        'status',
        'moderation_note',
        'price_cents',
        'compare_at_price_cents',
        'currency',
        'pay_what_you_want',
        'min_pwyw_cents',
        'license_key_prefix',
        'thumbnail_path',
        'preview_url',
        'sales_count',
        'views_count',
        'rating_avg',
        'rating_count',
        'is_featured',
        'published_at',
    ];

    protected $casts = [
        'price_cents' => 'integer',
        'compare_at_price_cents' => 'integer',
        'min_pwyw_cents' => 'integer',
        'pay_what_you_want' => 'boolean',
        'sales_count' => 'integer',
        'views_count' => 'integer',
        'rating_avg' => 'decimal:2',
        'rating_count' => 'integer',
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(ProductFile::class)->orderBy('sort_order');
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('sort_order');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('status', 'approved');
    }

    public function wishlistedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'wishlists')->withTimestamps();
    }

    public function coupons(): HasMany
    {
        return $this->hasMany(Coupon::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isPurchasable(): bool
    {
        return $this->isPublished() && $this->seller->isApproved();
    }

    public function getFormattedPrice(): string
    {
        return '$' . number_format($this->price_cents / 100, 2);
    }

    public function getFormattedCompareAtPrice(): ?string
    {
        return $this->compare_at_price_cents ? '$' . number_format($this->compare_at_price_cents / 100, 2) : null;
    }

    public function hasDiscount(): bool
    {
        return $this->compare_at_price_cents && $this->compare_at_price_cents > $this->price_cents;
    }

    public function getDiscountPercent(): ?int
    {
        if (!$this->hasDiscount()) {
            return null;
        }
        return round((($this->compare_at_price_cents - $this->price_cents) / $this->compare_at_price_cents) * 100);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where('published_at', '<=', now())
            ->whereHas('seller', fn($q) => $q->where('status', 'approved'));
    }
}