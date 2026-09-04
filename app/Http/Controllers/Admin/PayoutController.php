<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payout;
use App\Models\Seller;
use Illuminate\Http\Request;

class PayoutController extends Controller
{
    public function index(Request $request)
    {
        $query = Payout::with('seller.user');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payouts = $query->latest()->paginate(20)->withQueryString();

        return view('admin.payouts.index', compact('payouts'));
    }

    public function show(Payout $payout)
    {
        $payout->load('seller.user');
        return view('admin.payouts.show', compact('payout'));
    }

    public function process(Request $request, Payout $payout)
    {
        abort_unless($payout->isPending(), 403, 'Payout is not pending');

        $request->validate([
            'reference' => 'nullable|string|max:255',
        ]);

        $payout->markPaid($request->reference);

        // Notify seller
        // $payout->seller->user->notify(new PayoutProcessedNotification($payout));

        return back()->with('success', 'Payout marked as paid');
    }

    public function fail(Request $request, Payout $payout)
    {
        abort_unless($payout->isPending(), 403, 'Payout is not pending');

        $request->validate(['reason' => 'required|string']);

        $payout->update([
            'status' => 'failed',
            'details' => array_merge($payout->details ?? [], ['failure_reason' => $request->reason]),
        ]);

        // Return balance to seller
        $payout->seller->increment('balance_cents', $payout->amount_cents);

        return back()->with('success', 'Payout marked as failed, balance returned to seller');
    }

    public function bulkGenerate()
    {
        $sellers = Seller::where('status', 'approved')
            ->where('balance_cents', '>=', setting('commissions.seller_min_payout_cents', 5000))
            ->get();

        $count = 0;
        foreach ($sellers as $seller) {
            Payout::create([
                'seller_id' => $seller->id,
                'amount_cents' => $seller->balance_cents,
                'status' => 'pending',
                'method' => $seller->payout_method,
                'details' => $seller->payout_details,
                'period_start' => now()->startOfMonth(),
                'period_end' => now()->endOfMonth(),
            ]);
            $count++;
        }

        return back()->with('success', "Generated {$count} payout requests");
    }
}