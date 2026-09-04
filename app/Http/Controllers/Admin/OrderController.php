<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(protected OrderService $orderService) {}

    public function index(Request $request)
    {
        $query = Order::with(['user', 'items.product.seller']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('number', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%{$request->search}%"));
            });
        }

        $orders = $query->latest()->paginate(20)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.product.seller', 'items.downloads.productFile', 'transactions', 'coupon', 'posRegister', 'cashier']);
        return view('admin.orders.show', compact('order'));
    }

    public function markPaid(Request $request, Order $order)
    {
        abort_unless($order->status === 'pending', 403, 'Order is not pending');

        $request->validate([
            'gateway' => 'required|in:stripe,paypal,razorpay,cash,free,manual,bank_transfer',
            'reference' => 'nullable|string',
        ]);

        $this->orderService->markPaid($order, $request->gateway, $request->reference);

        return back()->with('success', 'Order marked as paid');
    }

    public function refund(Request $request, Order $order)
    {
        abort_unless($order->canBeRefunded(), 403, 'Order cannot be refunded');

        $request->validate(['reason' => 'required|string|max:1000']);

        $this->orderService->refund($order, $request->reason);

        return back()->with('success', 'Order refunded');
    }

    public function cancel(Order $order)
    {
        abort_unless($order->status === 'pending', 403, 'Only pending orders can be cancelled');

        $order->update(['status' => 'cancelled']);

        return back()->with('success', 'Order cancelled');
    }

    public function export(Request $request)
    {
        $orders = Order::with(['user', 'items.product'])->latest()->get();

        $headers = ['Order Number', 'Date', 'Customer', 'Email', 'Status', 'Total', 'Items', 'Source'];

        $callback = function () use ($orders, $headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);

            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->number,
                    $order->created_at->format('Y-m-d H:i:s'),
                    $order->user?->name ?? 'Guest',
                    $order->email,
                    $order->status,
                    format_price($order->total_cents, $order->currency),
                    $order->items->count(),
                    $order->source,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="orders-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }
}