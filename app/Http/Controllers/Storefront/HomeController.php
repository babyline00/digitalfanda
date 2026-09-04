<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProducts = Product::published()
            ->with(['seller', 'category'])
            ->where('is_featured', true)
            ->latest('published_at')
            ->take(8)
            ->get();

        $trendingProducts = Product::published()
            ->with(['seller', 'category'])
            ->orderByDesc('sales_count')
            ->take(8)
            ->get();

        $categories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->with('children')
            ->orderBy('sort_order')
            ->get();

        $newArrivals = Product::published()
            ->with(['seller', 'category'])
            ->latest('published_at')
            ->take(8)
            ->get();

        return view('storefront.home', compact(
            'featuredProducts',
            'trendingProducts',
            'categories',
            'newArrivals'
        ));
    }
}