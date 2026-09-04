<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $stats = [
            'orders_count' => $user->orders()->count(),
            'total_spent' => $user->orders()->where('status', 'paid')->sum('total_cents'),
            'downloads_available' => $user->orders()
                ->where('status', 'paid')
                ->with('items.downloads')
                ->get()
                ->flatMap->items
                ->flatMap->downloads
                ->filter(fn($d) => $d->isValid())
                ->count(),
            'wishlist_count' => $user->wishlists()->count(),
        ];

        $recentOrders = $user->orders()
            ->with(['items.product'])
            ->latest()
            ->take(5)
            ->get();

        $recommendations = Product::published()
            ->whereHas('category', fn($q) => $q->whereIn('id', 
                $user->orders()
                    ->where('status', 'paid')
                    ->with('items.product.category')
                    ->get()
                    ->flatMap->items
                    ->pluck('product.category_id')
                    ->unique()
            ))
            ->whereDoesntHave('orders', fn($q) => $q->where('user_id', $user->id))
            ->with(['seller', 'category'])
            ->inRandomOrder()
            ->take(4)
            ->get();

        return view('customer.dashboard', compact('stats', 'recentOrders', 'recommendations'));
    }
}