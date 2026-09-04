<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'title',
        'content',
        'video_url',
        'duration_min',
        'sort_order',
        'available_after_days',
    ];

    protected $casts = [
        'duration_min' => 'integer',
        'sort_order' => 'integer',
        'available_after_days' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function isAvailableForUser(User $user, ?Order $order = null): bool
    {
        if ($this->available_after_days === 0) {
            return true;
        }
        if (!$order) {
            return false;
        }
        $daysSincePurchase = $order->created_at->diffInDays(now());
        return $daysSincePurchase >= $this->available_after_days;
    }
}