<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index()
    {
        $products = auth()->user()->wishlists()
            ->with(['seller', 'category'])
            ->paginate(12);

        return view('storefront.wishlist.index', compact('products'));
    }

    public function toggle(Request $request, Product $product)
    {
        $user = $request->user();
        $wishlist = $user->wishlists();

        if ($wishlist->where('product_id', $product->id)->exists()) {
            $wishlist->detach($product->id);
            return response()->json(['added' => false, 'message' => 'Removed from wishlist']);
        }

        $wishlist->attach($product->id);
        return response()->json(['added' => true, 'message' => 'Added to wishlist']);
    }

    public function remove(Product $product)
    {
        auth()->user()->wishlists()->detach($product->id);
        return back()->with('success', 'Removed from wishlist');
    }
}