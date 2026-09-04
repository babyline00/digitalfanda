<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(protected CartService $cart) {}

    public function index()
    {
        $cart = $this->cart->getDetailed();
        return view('storefront.cart.index', compact('cart'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'integer|min:1|max:99',
            'variant_name' => 'nullable|string',
        ]);

        $product = Product::findOrFail($request->product_id);
        abort_unless($product->isPurchasable(), 403, 'Product is not available for purchase.');

        $cart = $this->cart->add(
            $product->id,
            $request->integer('quantity', 1),
            $request->variant_name
        );

        if ($request->wantsJson()) {
            return response()->json(['cart' => $cart, 'message' => 'Added to cart']);
        }

        return redirect()->route('cart.index')->with('success', 'Product added to cart');
    }

    public function update(Request $request, int $productId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0|max:99',
            'variant_name' => 'nullable|string',
        ]);

        $cart = $this->cart->update(
            $productId,
            $request->integer('quantity'),
            $request->variant_name
        );

        if ($request->wantsJson()) {
            return response()->json(['cart' => $cart]);
        }

        return redirect()->route('cart.index');
    }

    public function remove(Request $request, int $productId)
    {
        $variant = $request->variant_name;
        $cart = $this->cart->remove($productId, $variant);

        if ($request->wantsJson()) {
            return response()->json(['cart' => $cart]);
        }

        return redirect()->route('cart.index')->with('success', 'Item removed from cart');
    }

    public function clear()
    {
        $this->cart->clear();
        return redirect()->route('cart.index')->with('success', 'Cart cleared');
    }
}