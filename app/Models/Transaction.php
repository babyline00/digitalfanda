<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'gateway',
        'gateway_reference',
        'amount_cents',
        'currency',
        'status',
        'payload',
        'processed_at',
    ];

    protected $casts = [
        'amount_cents' => 'integer',
        'payload' => 'array',
        'processed_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function isSucceeded(): bool
    {
        return $this->status === 'succeeded';
    }

    public function markSucceeded(): void
    {
        $this->update([
            'status' => 'succeeded',
            'processed_at' => now(),
        ]);
    }
}