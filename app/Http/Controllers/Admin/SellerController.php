<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Seller;
use Illuminate\Http\Request;

class SellerController extends Controller
{
    public function index(Request $request)
    {
        $query = Seller::with('user');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('kyc_status')) {
            $query->where('kyc_status', $request->kyc_status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('store_name', 'like', "%{$request->search}%")
                  ->orWhere('slug', 'like', "%{$request->search}%")
                  ->orWhereHas('user', fn($uq) => $uq->where('email', 'like', "%{$request->search}%"));
            });
        }

        $sellers = $query->latest()->paginate(20)->withQueryString();

        return view('admin.sellers.index', compact('sellers'));
    }

    public function show(Seller $seller)
    {
        $seller->load(['user', 'products', 'orders' => fn($q) => $q->latest()->take(10), 'payouts']);
        return view('admin.sellers.show', compact('seller'));
    }

    public function approve(Seller $seller)
    {
        $seller->update([
            'status' => 'approved',
            'approved_at' => now(),
            'kyc_status' => 'verified',
        ]);

        // Notify seller
        // $seller->user->notify(new SellerApprovedNotification());

        return back()->with('success', 'Seller approved');
    }

    public function reject(Request $request, Seller $seller)
    {
        $request->validate(['reason' => 'required|string']);

        $seller->update([
            'status' => 'rejected',
            'moderation_note' => $request->reason,
        ]);

        // Notify seller
        // $seller->user->notify(new SellerRejectedNotification($request->reason));

        return back()->with('success', 'Seller rejected');
    }

    public function suspend(Request $request, Seller $seller)
    {
        $request->validate(['reason' => 'required|string']);

        $seller->update([
            'status' => 'suspended',
            'suspended_at' => now(),
            'moderation_note' => $request->reason,
        ]);

        return back()->with('success', 'Seller suspended');
    }

    public function activate(Seller $seller)
    {
        $seller->update([
            'status' => 'approved',
            'suspended_at' => null,
        ]);

        return back()->with('success', 'Seller activated');
    }

    public function updateCommission(Request $request, Seller $seller)
    {
        $request->validate([
            'commission_rate' => 'required|numeric|min:0|max:100',
        ]);

        $seller->update(['commission_rate' => $request->commission_rate]);

        return back()->with('success', 'Commission rate updated');
    }
}