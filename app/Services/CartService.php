<?php

namespace App\Services;

use App\Models\Product;

class CartService
{
    protected string $sessionKey = 'cart';

    public function get(): array
    {
        return session()->get($this->sessionKey, []);
    }

    public function add(int $productId, int $quantity = 1, string $variantName = null): array
    {
        $cart = $this->get();
        $key = $variantName ? "{$productId}:{$variantName}" : (string) $productId;

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += $quantity;
        } else {
            $cart[$key] = [
                'product_id' => $productId,
                'variant_name' => $variantName,
                'quantity' => $quantity,
            ];
        }

        session()->put($this->sessionKey, $cart);
        return $this->getDetailed();
    }

    public function update(int $productId, int $quantity, string $variantName = null): array
    {
        $cart = $this->get();
        $key = $variantName ? "{$productId}:{$variantName}" : (string) $productId;

        if ($quantity <= 0) {
            unset($cart[$key]);
        } elseif (isset($cart[$key])) {
            $cart[$key]['quantity'] = $quantity;
        }

        session()->put($this->sessionKey, $cart);
        return $this->getDetailed();
    }

    public function remove(int $productId, string $variantName = null): array
    {
        $cart = $this->get();
        $key = $variantName ? "{$productId}:{$variantName}" : (string) $productId;
        unset($cart[$key]);
        session()->put($this->sessionKey, $cart);
        return $this->getDetailed();
    }

    public function clear(): void
    {
        session()->forget($this->sessionKey);
    }

    public function getDetailed(): array
    {
        $cart = $this->get();
        if (empty($cart)) {
            return ['items' => [], 'subtotal' => 0, 'count' => 0];
        }

        $productIds = array_column($cart, 'product_id');
        $products = Product::with('seller')->whereIn('id', $productIds)->get()->keyBy('id');

        $items = [];
        $subtotal = 0;
        $count = 0;

        foreach ($cart as $key => $item) {
            $product = $products->get($item['product_id']);
            if (!$product || !$product->isPurchasable()) {
                continue;
            }

            $lineTotal = $product->price_cents * $item['quantity'];
            $items[] = [
                'key' => $key,
                'product' => $product,
                'variant_name' => $item['variant_name'],
                'quantity' => $item['quantity'],
                'unit_price' => $product->price_cents,
                'total' => $lineTotal,
            ];
            $subtotal += $lineTotal;
            $count += $item['quantity'];
        }

        return compact('items', 'subtotal', 'count');
    }

    public function mergeWithUserCart(User $user): void
    {
        // If user has a persistent cart, could merge here
    }
}