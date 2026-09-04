<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PosRegister;
use App\Models\Product;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PosController extends Controller
{
    public function __construct(
        protected CartService $cart,
        protected OrderService $orderService
    ) {}

    public function registers()
    {
        $registers = PosRegister::with(['opener', 'closer'])->latest()->paginate(15);
        return view('admin.pos.registers', compact('registers'));
    }

    public function openRegister(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'opening_float_cents' => 'nullable|integer|min:0',
        ]);

        $register = PosRegister::create([
            'name' => $request->name,
            'location' => $request->location,
            'opened_by' => Auth::id(),
            'opening_float_cents' => $request->opening_float_cents ?? 0,
            'status' => 'open',
            'opened_at' => now(),
        ]);

        return redirect()->route('pos.register', $register);
    }

    public function register(PosRegister $register)
    {
        abort_unless($register->isOpen(), 403, 'Register is not open');

        $products = Product::published()
            ->where('type', '!=', 'course')
            ->with(['seller'])
            ->orderBy('title')
            ->get();

        $cart = $this->cart->getDetailed();

        return view('pos.register', compact('register', 'products', 'cart'));
    }

    public function addToCart(Request $request, PosRegister $register)
    {
        abort_unless($register->isOpen(), 403);

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'integer|min:1|max:99',
        ]);

        $product = Product::findOrFail($request->product_id);
        abort_unless($product->isPurchasable(), 403);

        $this->cart->add($product->id, $request->integer('quantity', 1));

        return response()->json(['cart' => $this->cart->getDetailed()]);
    }

    public function updateCart(Request $request, PosRegister $register)
    {
        abort_unless($register->isOpen(), 403);

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:0|max:99',
        ]);

        $this->cart->update($request->product_id, $request->quantity);

        return response()->json(['cart' => $this->cart->getDetailed()]);
    }

    public function clearCart(PosRegister $register)
    {
        abort_unless($register->isOpen(), 403);
        $this->cart->clear();
        return response()->json(['cart' => $this->cart->getDetailed()]);
    }

    public function checkout(Request $request, PosRegister $register)
    {
        abort_unless($register->isOpen(), 403);

        $cart = $this->cart->getDetailed();
        abort_if(empty($cart['items']), 403, 'Cart is empty');

        $request->validate([
            'customer_email' => 'nullable|email',
            'customer_name' => 'nullable|string|max:255',
            'payment_method' => 'required|in:cash,card,free,manual',
            'amount_tendered_cents' => 'nullable|integer|min:0',
        ]);

        $email = $request->customer_email ?? 'pos@sale.local';
        $name = $request->customer_name ?? 'POS Customer';

        try {
            $order = $this->orderService->createFromCart(
                $cart['items'],
                null, // guest
                $email,
                [
                    'source' => 'pos',
                    'pos_register_id' => $register->id,
                    'cashier_id' => Auth::id(),
                    'currency' => 'USD',
                    'billing_info' => [
                        'customer_name' => $name,
                        'payment_method' => $request->payment_method,
                    ],
                ]
            );

            // For POS, immediately mark as paid
            $gateway = match ($request->payment_method) {
                'cash' => 'cash',
                'card' => 'stripe', // would redirect to terminal
                'free' => 'free',
                'manual' => 'manual',
            };

            $this->orderService->markPaid($order, $gateway);

            $this->cart->clear();

            return response()->json([
                'success' => true,
                'order' => $order->load('items.product'),
                'receipt_url' => route('pos.receipt', $order),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function receipt(Order $order)
    {
        $order->load('items.product');
        return view('pos.receipt', compact('order'));
    }

    public function closeRegister(Request $request, PosRegister $register)
    {
        abort_unless($register->isOpen(), 403);

        $request->validate([
            'declared_cash_cents' => 'required|integer|min:0',
        ]);

        $register->close(Auth::user(), $request->declared_cash_cents);

        return redirect()->route('admin.pos.registers')
            ->with('success', 'Register closed. Cash difference: ' . format_price($register->cash_difference_cents));
    }

    public function salesReport(PosRegister $register)
    {
        $orders = $register->orders()
            ->where('source', 'pos')
            ->where('status', 'paid')
            ->with(['items.product', 'cashier', 'transactions'])
            ->latest()
            ->paginate(50);

        $summary = [
            'total_orders' => $orders->total(),
            'total_revenue' => $orders->sum('total_cents'),
            'cash_payments' => $orders->whereHas('transactions', fn($q) => $q->where('gateway', 'cash'))->sum('total_cents'),
            'card_payments' => $orders->whereHas('transactions', fn($q) => $q->where('gateway', 'stripe'))->sum('total_cents'),
        ];

        return view('pos.report', compact('register', 'orders', 'summary'));
    }
}