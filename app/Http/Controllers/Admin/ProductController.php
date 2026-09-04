<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['seller.user', 'category']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('seller_id')) {
            $query->where('seller_id', $request->seller_id);
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        $products = $query->latest()->paginate(20)->withQueryString();

        return view('admin.products.index', compact('products'));
    }

    public function show(Product $product)
    {
        $product->load(['seller.user', 'category', 'files', 'lessons', 'orders' => fn($q) => $q->latest()->take(10)]);
        return view('admin.products.show', compact('product'));
    }

    public function approve(Product $product)
    {
        $product->update([
            'status' => 'published',
            'published_at' => now(),
            'moderation_note' => null,
        ]);

        return back()->with('success', 'Product published');
    }

    public function reject(Request $request, Product $product)
    {
        $request->validate(['reason' => 'required|string']);

        $product->update([
            'status' => 'rejected',
            'moderation_note' => $request->reason,
        ]);

        return back()->with('success', 'Product rejected');
    }

    public function feature(Product $product)
    {
        $product->update(['is_featured' => !$product->is_featured]);
        return back()->with('success', $product->is_featured ? 'Product featured' : 'Product unfeatured');
    }

    public function archive(Product $product)
    {
        $product->update(['status' => 'archived']);
        return back()->with('success', 'Product archived');
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:approve,reject,archive,delete,feature,unfeature',
            'ids' => 'required|array',
            'ids.*' => 'exists:products,id',
        ]);

        $products = Product::whereIn('id', $request->ids);

        match ($request->action) {
            'approve' => $products->update(['status' => 'published', 'published_at' => now()]),
            'reject' => $products->update(['status' => 'rejected']),
            'archive' => $products->update(['status' => 'archived']),
            'delete' => $products->delete(),
            'feature' => $products->update(['is_featured' => true]),
            'unfeature' => $products->update(['is_featured' => false]),
        };

        return back()->with('success', "Action {$request->action} completed for " . $request->ids . " products");
    }
}