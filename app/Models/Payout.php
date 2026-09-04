<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payout extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'amount_cents',
        'status',
        'method',
        'reference',
        'details',
        'period_start',
        'period_end',
        'processed_at',
    ];

    protected $casts = [
        'amount_cents' => 'integer',
        'details' => 'array',
        'period_start' => 'date',
        'period_end' => 'date',
        'processed_at' => 'datetime',
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function markPaid(string $reference = null): void
    {
        $this->update([
            'status' => 'paid',
            'reference' => $reference,
            'processed_at' => now(),
        ]);
    }
}