<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::published()->with(['seller', 'category']);

        // Category filter
        if ($request->filled('category')) {
            $category = Category::where('slug', $request->category)->firstOrFail();
            $query->where(function ($q) use ($category) {
                $q->where('category_id', $category->id)
                  ->orWhereHas('category', fn($sq) => $sq->where('parent_id', $category->id));
            });
        }

        // Search
        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->q}%")
                  ->orWhere('description', 'like', "%{$request->q}%");
            });
        }

        // Price range
        if ($request->filled('min_price')) {
            $query->where('price_cents', '>=', $request->min_price * 100);
        }
        if ($request->filled('max_price')) {
            $query->where('price_cents', '<=', $request->max_price * 100);
        }

        // Type filter
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Sort
        $sort = $request->get('sort', 'popularity');
        match ($sort) {
            'newest' => $query->latest('published_at'),
            'price_asc' => $query->orderBy('price_cents'),
            'price_desc' => $query->orderByDesc('price_cents'),
            'rating' => $query->orderByDesc('rating_avg'),
            default => $query->orderByDesc('sales_count'), // popularity
        };

        $products = $query->paginate(12)->withQueryString();

        $categories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->with('children')
            ->orderBy('sort_order')
            ->get();

        return view('storefront.catalog.index', compact('products', 'categories'));
    }

    public function category(Category $category)
    {
        return $this->index(request()->merge(['category' => $category->slug]));
    }
}