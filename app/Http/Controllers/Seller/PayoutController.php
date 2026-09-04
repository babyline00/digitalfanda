<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Payout;
use App\Services\PayoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayoutController extends Controller
{
    public function __construct(protected PayoutService $payoutService) {}

    public function index()
    {
        $seller = Auth::user()->seller;

        $payouts = $seller->payouts()->latest()->paginate(15);
        $availableBalance = $seller->balance_cents;
        $minPayout = setting('commissions.seller_min_payout_cents', 5000);
        $schedule = $this->payoutService->getPayoutSchedule();

        return view('seller.payouts.index', compact('payouts', 'availableBalance', 'minPayout', 'schedule'));
    }

    public function request(Request $request)
    {
        $seller = Auth::user()->seller;

        $request->validate([
            'method' => 'nullable|in:paypal,bank,stripe_connect,manual',
        ]);

        try {
            $payout = $this->payoutService->requestPayout($seller, $request->method);
            return back()->with('success', 'Payout requested. It will be processed according to the schedule.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function show(Payout $payout)
    {
        $this->authorize('view', $payout->seller);
        return view('seller.payouts.show', compact('payout'));
    }

    public function settings()
    {
        $seller = Auth::user()->seller;

        return view('seller.payouts.settings', compact('seller'));
    }

    public function updateSettings(Request $request)
    {
        $seller = Auth::user()->seller;

        $request->validate([
            'payout_email' => 'nullable|email',
            'payout_method' => 'required|in:paypal,bank,stripe_connect',
            'payout_details' => 'nullable|array',
        ]);

        $seller->update($request->only(['payout_email', 'payout_method', 'payout_details']));

        return back()->with('success', 'Payout settings updated');
    }
}