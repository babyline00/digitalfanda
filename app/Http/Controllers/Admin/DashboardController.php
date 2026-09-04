<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payout;
use App\Models\Product;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_sellers' => Seller::count(),
            'pending_sellers' => Seller::where('status', 'pending')->count(),
            'total_products' => Product::count(),
            'pending_products' => Product::where('status', 'pending')->count(),
            'total_orders' => Order::count(),
            'paid_orders' => Order::where('status', 'paid')->count(),
            'total_revenue' => Order::where('status', 'paid')->sum('total_cents'),
            'platform_fees' => Order::where('status', 'paid')->sum('fee_cents'),
            'pending_payouts' => Payout::where('status', 'pending')->sum('amount_cents'),
            'monthly_revenue' => Order::where('status', 'paid')
                ->whereMonth('paid_at', now()->month)
                ->sum('total_cents'),
        ];

        $recentSellers = Seller::with('user')->latest()->take(5)->get();
        $recentProducts = Product::with('seller')->latest()->take(5)->get();
        $recentOrders = Order::with('user')->latest()->take(5)->get();
        $recentPayouts = Payout::with('seller.user')->latest()->take(5)->get();

        // Chart data
        $revenueChart = Order::where('status', 'paid')
            ->where('paid_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(paid_at) as date, SUM(total_cents) as revenue, COUNT(*) as orders')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $sellerChart = Seller::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recentSellers',
            'recentProducts',
            'recentOrders',
            'recentPayouts',
            'revenueChart',
            'sellerChart'
        ));
    }
}