<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Order;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function __construct(
        protected CartService $cart,
        protected OrderService $orderService
    ) {}

    public function index()
    {
        $cart = $this->cart->getDetailed();
        abort_if(empty($cart['items']), 403, 'Your cart is empty');

        return view('storefront.checkout.index', compact('cart'));
    }

    public function process(Request $request)
    {
        $cart = $this->cart->getDetailed();
        abort_if(empty($cart['items']), 403, 'Your cart is empty');

        $request->validate([
            'email' => 'required|email',
            'name' => 'required|string|max:255',
            'coupon_code' => 'nullable|string',
            'gateway' => 'required|in:stripe,paypal,razorpay,bank_transfer,free',
            'billing_info' => 'nullable|array',
        ]);

        $user = Auth::user();
        $email = $user?->email ?? $request->email;

        $coupon = null;
        if ($request->filled('coupon_code')) {
            $coupon = Coupon::where('code', $request->coupon_code)->first();
            if (!$coupon || !$coupon->isValid($cart['subtotal'])) {
                return back()->with('error', 'Invalid or expired coupon code.');
            }
        }

        try {
            $order = $this->orderService->createFromCart(
                $cart['items'],
                $user,
                $email,
                [
                    'coupon_code' => $request->coupon_code,
                    'source' => 'web',
                    'currency' => 'USD',
                    'billing_info' => $request->billing_info,
                ]
            );

            $this->cart->clear();

            // Redirect to payment gateway
            if ($order->total_cents > 0) {
                $session = $this->orderService->getPaymentManager()->createSession(
                    $order,
                    $request->gateway
                );

                if ($session['requires_redirect']) {
                    return redirect($session['url']);
                }
            }

            return redirect()->route('checkout.success', $order->number)
                ->with('success', 'Order placed successfully!');
        } catch (\Throwable $e) {
            return back()->with('error', 'Order failed: ' . $e->getMessage());
        }
    }

    public function success(string $number)
    {
        $order = Order::where('number', $number)
            ->where(function ($q) {
                $user = auth()->user();
                if ($user) {
                    $q->where('user_id', $user->id)->orWhere('email', $user->email);
                }
            })
            ->with(['items.product', 'items.seller'])
            ->firstOrFail();

        return view('storefront.checkout.success', compact('order'));
    }

    public function applyCoupon(Request $request)
    {
        $cart = $this->cart->getDetailed();
        abort_if(empty($cart['items']), 403);

        $request->validate(['code' => 'required|string']);

        $coupon = Coupon::where('code', $request->code)->first();

        if (!$coupon || !$coupon->isValid($cart['subtotal'])) {
            return response()->json(['valid' => false, 'message' => 'Invalid or expired coupon']);
        }

        $discount = $coupon->calculateDiscount($cart['subtotal']);

        return response()->json([
            'valid' => true,
            'discount' => $discount,
            'formatted_discount' => format_price($discount),
            'message' => 'Coupon applied!',
        ]);
    }
}