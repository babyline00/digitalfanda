<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Auth::user()->seller->products()->with(['category', 'files']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        $products = $query->latest()->paginate(15)->withQueryString();

        return view('seller.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('seller.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $seller = Auth::user()->seller;

        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'type' => 'required|in:download,stream,course,license_key',
            'price_cents' => 'required|integer|min:0',
            'compare_at_price_cents' => 'nullable|integer|min:0',
            'currency' => 'required|string|size:3',
            'pay_what_you_want' => 'boolean',
            'min_pwyw_cents' => 'nullable|integer|min:0',
            'license_key_prefix' => 'nullable|string|max:10',
            'files' => 'nullable|array',
            'files.*' => 'file|max:102400',
            'thumbnail' => 'nullable|image|max:2048',
        ]);

        $data = $request->only([
            'title', 'subtitle', 'description', 'category_id', 'type',
            'price_cents', 'compare_at_price_cents', 'currency',
            'pay_what_you_want', 'min_pwyw_cents', 'license_key_prefix'
        ]);

        $data['seller_id'] = $seller->id;
        $data['slug'] = Str::slug($data['title']) . '-' . Str::random(6);
        $data['status'] = 'pending';

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail_path'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        $product = Product::create($data);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $index => $file) {
                $path = $file->store('products/' . $product->id, 'private');
                ProductFile::create([
                    'product_id' => $product->id,
                    'name' => $file->getClientOriginalName(),
                    'disk' => 'private',
                    'path' => $path,
                    'size_bytes' => $file->getSize(),
                    'mime' => $file->getMimeType(),
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()->route('seller.products.index')
            ->with('success', 'Product created and submitted for review.');
    }

    public function edit(Product $product)
    {
        $this->authorize('update', $product);
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $product->load('files');
        return view('seller.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $this->authorize('update', $product);

        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'type' => 'required|in:download,stream,course,license_key',
            'price_cents' => 'required|integer|min:0',
            'compare_at_price_cents' => 'nullable|integer|min:0',
            'currency' => 'required|string|size:3',
            'pay_what_you_want' => 'boolean',
            'min_pwyw_cents' => 'nullable|integer|min:0',
            'license_key_prefix' => 'nullable|string|max:10',
            'thumbnail' => 'nullable|image|max:2048',
        ]);

        $data = $request->only([
            'title', 'subtitle', 'description', 'category_id', 'type',
            'price_cents', 'compare_at_price_cents', 'currency',
            'pay_what_you_want', 'min_pwyw_cents', 'license_key_prefix'
        ]);

        if ($request->hasFile('thumbnail')) {
            if ($product->thumbnail_path) {
                Storage::disk('public')->delete($product->thumbnail_path);
            }
            $data['thumbnail_path'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        // If status was published, set back to pending for re-review
        if ($product->status === 'published') {
            $data['status'] = 'pending';
        }

        $product->update($data);

        return redirect()->route('seller.products.index')
            ->with('success', 'Product updated' . ($data['status'] === 'pending' ? ' and submitted for review' : ''));
    }

    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);

        foreach ($product->files as $file) {
            Storage::disk($file->disk)->delete($file->path);
        }
        if ($product->thumbnail_path) {
            Storage::disk('public')->delete($product->thumbnail_path);
        }

        $product->delete();

        return redirect()->route('seller.products.index')
            ->with('success', 'Product deleted');
    }

    public function addFile(Request $request, Product $product)
    {
        $this->authorize('update', $product);

        $request->validate([
            'file' => 'required|file|max:102400',
            'name' => 'required|string|max:255',
        ]);

        $file = $request->file('file');
        $path = $file->store('products/' . $product->id, 'private');

        ProductFile::create([
            'product_id' => $product->id,
            'name' => $request->name,
            'disk' => 'private',
            'path' => $path,
            'size_bytes' => $file->getSize(),
            'mime' => $file->getMimeType(),
            'sort_order' => $product->files()->max('sort_order') + 1,
        ]);

        return back()->with('success', 'File added');
    }

    public function removeFile(Product $product, ProductFile $file)
    {
        $this->authorize('update', $product);

        Storage::disk($file->disk)->delete($file->path);
        $file->delete();

        return back()->with('success', 'File removed');
    }

    public function addLesson(Request $request, Product $product)
    {
        $this->authorize('update', $product);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'video_url' => 'nullable|url',
            'duration_min' => 'nullable|integer|min:0',
            'sort_order' => 'nullable|integer|min:0',
            'available_after_days' => 'nullable|integer|min:0',
        ]);

        $product->lessons()->create($request->only([
            'title', 'content', 'video_url', 'duration_min', 'sort_order', 'available_after_days'
        ]));

        return back()->with('success', 'Lesson added');
    }

    public function updateLesson(Request $request, Product $product, $lessonId)
    {
        $this->authorize('update', $product);

        $lesson = $product->lessons()->findOrFail($lessonId);
        $lesson->update($request->only([
            'title', 'content', 'video_url', 'duration_min', 'sort_order', 'available_after_days'
        ]));

        return back()->with('success', 'Lesson updated');
    }

    public function deleteLesson(Product $product, $lessonId)
    {
        $this->authorize('update', $product);
        $product->lessons()->findOrFail($lessonId)->delete();
        return back()->with('success', 'Lesson deleted');
    }
}