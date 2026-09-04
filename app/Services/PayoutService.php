<?php

namespace App\Services;

use App\Models\Payout;
use App\Models\Seller;
use Carbon\Carbon;

class PayoutService
{
    public function calculatePendingEarnings(Seller $seller): int
    {
        return $seller->balance_cents;
    }

    public function requestPayout(Seller $seller, string $method = null): Payout
    {
        $amount = $this->calculatePendingEarnings($seller);
        $minPayout = setting('commissions.seller_min_payout_cents', 5000);

        if ($amount < $minPayout) {
            throw new \Exception("Minimum payout amount is " . format_price($minPayout));
        }

        $method = $method ?? $seller->payout_method ?? 'paypal';

        $periodStart = Carbon::now()->startOfMonth();
        $periodEnd = Carbon::now()->endOfMonth();

        return Payout::create([
            'seller_id' => $seller->id,
            'amount_cents' => $amount,
            'status' => 'pending',
            'method' => $method,
            'details' => $seller->payout_details,
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
        ]);
    }

    public function processPayout(Payout $payout, string $reference = null): void
    {
        $payout->seller->decrement('balance_cents', $payout->amount_cents);
        $payout->markPaid($reference);
    }

    public function getPayoutSchedule(): array
    {
        return [
            'frequency' => setting('commissions.payout_frequency', 'monthly'),
            'minimum' => setting('commissions.seller_min_payout_cents', 5000),
            'delay_days' => setting('commissions.payout_delay_days', 14),
        ];
    }

    public function generateBatch(): array
    {
        $sellers = Seller::where('status', 'approved')
            ->where('balance_cents', '>=', setting('commissions.seller_min_payout_cents', 5000))
            ->get();

        $payouts = [];
        foreach ($sellers as $seller) {
            $payouts[] = $this->requestPayout($seller);
        }

        return $payouts;
    }
}