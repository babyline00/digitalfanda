<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show(Product $product)
    {
        abort_if(!$product->isPurchasable(), 404);

        $product->increment('views_count');

        $product->load(['seller.user', 'category', 'files', 'lessons', 'reviews.user']);

        $relatedProducts = Product::published()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with(['seller', 'category'])
            ->inRandomOrder()
            ->take(4)
            ->get();

        $reviews = $product->reviews()->latest()->paginate(10);

        $userReview = null;
        if (auth()->check()) {
            $userReview = $product->reviews()->where('user_id', auth()->id())->first();
        }

        return view('storefront.product.show', compact(
            'product',
            'relatedProducts',
            'reviews',
            'userReview'
        ));
    }

    public function storeReview(Request $request, Product $product)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'nullable|string',
        ]);

        // Check if user purchased this product
        $hasPurchased = $request->user()->orders()
            ->whereHas('items', fn($q) => $q->where('product_id', $product->id))
            ->where('status', 'paid')
            ->exists();

        abort_unless($hasPurchased, 403, 'You can only review products you have purchased.');

        $existing = $product->reviews()->where('user_id', $request->user()->id)->first();
        if ($existing) {
            return back()->with('error', 'You have already reviewed this product.');
        }

        $review = Review::create([
            'product_id' => $product->id,
            'user_id' => $request->user()->id,
            'rating' => $request->rating,
            'title' => $request->title,
            'comment' => $request->comment,
            'status' => 'approved',
        ]);

        // Update product rating
        $avg = $product->reviews()->where('status', 'approved')->avg('rating');
        $count = $product->reviews()->where('status', 'approved')->count();
        $product->update([
            'rating_avg' => round($avg, 2),
            'rating_count' => $count,
        ]);

        return back()->with('success', 'Thank you for your review!');
    }
}