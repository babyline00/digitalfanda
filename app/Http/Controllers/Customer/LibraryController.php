<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LibraryController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Product::whereHas('orders', function ($q) use ($user) {
            $q->where('user_id', $user->id)
              ->where('status', 'paid');
        })->with(['seller', 'files', 'lessons', 'orders' => fn($q) => $q->where('user_id', $user->id)->where('status', 'paid')->latest()]);

        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $products = $query->paginate(12)->withQueryString();

        return view('customer.library', compact('products'));
    }

    public function show(Product $product)
    {
        $user = Auth::user();

        $order = $user->orders()
            ->where('status', 'paid')
            ->whereHas('items', fn($q) => $q->where('product_id', $product->id))
            ->with('items.downloads.productFile')
            ->firstOrFail();

        $orderItem = $order->items->firstWhere('product_id', $product->id);

        $product->load(['files', 'lessons']);

        return view('customer.library.show', compact('product', 'order', 'orderItem'));
    }
}