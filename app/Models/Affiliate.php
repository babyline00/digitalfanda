<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Affiliate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'code',
        'commission_rate',
        'balance_cents',
        'lifetime_earnings_cents',
        'clicks',
        'conversions',
    ];

    protected $casts = [
        'commission_rate' => 'decimal:2',
        'balance_cents' => 'integer',
        'lifetime_earnings_cents' => 'integer',
        'clicks' => 'integer',
        'conversions' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function clicks(): HasMany
    {
        return $this->hasMany(AffiliateClick::class);
    }

    public function conversions(): HasMany
    {
        return $this->hasMany(AffiliateConversion::class);
    }

    public function addEarnings(int $cents): void
    {
        $this->increment('balance_cents', $cents);
        $this->increment('lifetime_earnings_cents', $cents);
    }
}