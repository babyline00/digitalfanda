<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Auth::user()->seller->coupons()->latest()->paginate(15);
        return view('seller.coupons.index', compact('coupons'));
    }

    public function create()
    {
        return view('seller.coupons.create');
    }

    public function store(Request $request)
    {
        $seller = Auth::user()->seller;

        $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code',
            'type' => 'required|in:percent,fixed',
            'value' => 'required|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'usage_limit' => 'nullable|integer|min:1',
            'min_subtotal_cents' => 'nullable|integer|min:0',
        ]);

        $coupon = $seller->coupons()->create([
            'code' => strtoupper($request->code),
            'type' => $request->type,
            'value' => $request->value,
            'starts_at' => $request->starts_at,
            'expires_at' => $request->expires_at,
            'usage_limit' => $request->usage_limit,
            'min_subtotal_cents' => $request->min_subtotal_cents ?? 0,
            'is_active' => true,
        ]);

        return redirect()->route('seller.coupons.index')->with('success', 'Coupon created');
    }

    public function edit(Coupon $coupon)
    {
        $this->authorize('update', $coupon);
        return view('seller.coupons.edit', compact('coupon'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $this->authorize('update', $coupon);

        $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
            'type' => 'required|in:percent,fixed',
            'value' => 'required|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'usage_limit' => 'nullable|integer|min:1',
            'min_subtotal_cents' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $coupon->update($request->only([
            'code', 'type', 'value', 'starts_at', 'expires_at', 'usage_limit', 'min_subtotal_cents', 'is_active'
        ]));

        return redirect()->route('seller.coupons.index')->with('success', 'Coupon updated');
    }

    public function destroy(Coupon $coupon)
    {
        $this->authorize('delete', $coupon);
        $coupon->delete();
        return back()->with('success', 'Coupon deleted');
    }

    public function generateCode()
    {
        $code = strtoupper(Str::random(8));
        while (Coupon::where('code', $code)->exists()) {
            $code = strtoupper(Str::random(8));
        }
        return response()->json(['code' => $code]);
    }
}