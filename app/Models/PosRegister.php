<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PosRegister extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'opened_by',
        'closed_by',
        'status',
        'opening_float_cents',
        'expected_cash_cents',
        'declared_cash_cents',
        'cash_difference_cents',
        'opened_at',
        'closed_at',
    ];

    protected $casts = [
        'opening_float_cents' => 'integer',
        'expected_cash_cents' => 'integer',
        'declared_cash_cents' => 'integer',
        'cash_difference_cents' => 'integer',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function opener(): BelongsTo
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    public function closer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function open(User $user, int $floatCents = 0): void
    {
        $this->update([
            'status' => 'open',
            'opened_by' => $user->id,
            'opening_float_cents' => $floatCents,
            'opened_at' => now(),
        ]);
    }

    public function close(User $user, int $declaredCashCents): void
    {
        $cashOrders = $this->orders()
            ->where('source', 'pos')
            ->where('status', 'paid')
            ->whereHas('transactions', fn($q) => $q->where('gateway', 'cash'))
            ->sum('total_cents');

        $this->update([
            'status' => 'closed',
            'closed_by' => $user->id,
            'expected_cash_cents' => $cashOrders + $this->opening_float_cents,
            'declared_cash_cents' => $declaredCashCents,
            'cash_difference_cents' => $declaredCashCents - ($cashOrders + $this->opening_float_cents),
            'closed_at' => now(),
        ]);
    }
}