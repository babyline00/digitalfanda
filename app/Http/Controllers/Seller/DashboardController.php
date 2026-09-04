<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Payout;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $seller = Auth::user()->seller;

        $stats = [
            'total_products' => $seller->products()->count(),
            'published_products' => $seller->products()->where('status', 'published')->count(),
            'pending_products' => $seller->products()->where('status', 'pending')->count(),
            'total_sales' => $seller->total_sales_cents,
            'available_balance' => $seller->balance_cents,
            'pending_payouts' => $seller->payouts()->where('status', 'pending')->sum('amount_cents'),
            'orders_this_month' => OrderItem::where('seller_id', $seller->id)
                ->whereHas('order', fn($q) => $q->where('status', 'paid')
                    ->whereMonth('paid_at', now()->month))
                ->count(),
            'revenue_this_month' => OrderItem::where('seller_id', $seller->id)
                ->whereHas('order', fn($q) => $q->where('status', 'paid')
                    ->whereMonth('paid_at', now()->month))
                ->sum('seller_earnings_cents'),
        ];

        $recentOrders = OrderItem::where('seller_id', $seller->id)
            ->with(['order.user', 'product'])
            ->latest()
            ->take(10)
            ->get();

        $topProducts = Product::where('seller_id', $seller->id)
            ->withCount(['orderItems as sales_count' => fn($q) => $q->whereHas('order', fn($oq) => $oq->where('status', 'paid'))])
            ->orderByDesc('sales_count')
            ->take(5)
            ->get();

        $recentPayouts = $seller->payouts()->latest()->take(5)->get();

        return view('seller.dashboard', compact('stats', 'recentOrders', 'topProducts', 'recentPayouts'));
    }
}