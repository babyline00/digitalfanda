<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        $seller = Auth::user()->seller;

        $query = OrderItem::where('seller_id', $seller->id)
            ->with(['order.user', 'product'])
            ->latest();

        if ($request->filled('status')) {
            $query->whereHas('order', fn($q) => $q->where('status', $request->status));
        }

        if ($request->filled('date_from')) {
            $query->whereHas('order', fn($q) => $q->whereDate('created_at', '>=', $request->date_from));
        }

        if ($request->filled('date_to')) {
            $query->whereHas('order', fn($q) => $q->whereDate('created_at', '<=', $request->date_to));
        }

        $sales = $query->paginate(20)->withQueryString();

        return view('seller.sales.index', compact('sales'));
    }

    public function show(OrderItem $sale)
    {
        $this->authorize('view', $sale->order);
        $sale->load(['order.user', 'product', 'downloads.productFile']);
        return view('seller.sales.show', compact('sale'));
    }

    public function analytics(Request $request)
    {
        $seller = Auth::user()->seller;

        $days = $request->integer('days', 30);
        $startDate = now()->subDays($days);

        $dailyRevenue = OrderItem::where('seller_id', $seller->id)
            ->whereHas('order', fn($q) => $q->where('status', 'paid')->whereDate('paid_at', '>=', $startDate))
            ->selectRaw('DATE(orders.paid_at) as date, SUM(order_items.seller_earnings_cents) as revenue, COUNT(*) as sales')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $productPerformance = Product::where('seller_id', $seller->id)
            ->withCount(['orderItems as sales_count' => fn($q) => $q->whereHas('order', fn($oq) => $oq->where('status', 'paid')->whereDate('created_at', '>=', $startDate))])
            ->withSum(['orderItems as revenue_cents' => fn($q) => $q->whereHas('order', fn($oq) => $oq->where('status', 'paid')->whereDate('created_at', '>=', $startDate))], 'seller_earnings_cents')
            ->orderByDesc('sales_count')
            ->take(10)
            ->get();

        return view('seller.sales.analytics', compact('dailyRevenue', 'productPerformance', 'days'));
    }

    public function export(Request $request)
    {
        $seller = Auth::user()->seller;

        $sales = OrderItem::where('seller_id', $seller->id)
            ->whereHas('order', fn($q) => $q->where('status', 'paid'))
            ->with(['order.user', 'product'])
            ->latest()
            ->get();

        $headers = [
            'Order Number', 'Date', 'Customer', 'Product', 'Quantity', 'Unit Price', 'Total', 'Your Earnings', 'Status'
        ];

        $callback = function () use ($sales, $headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);

            foreach ($sales as $sale) {
                fputcsv($file, [
                    $sale->order->number,
                    $sale->order->paid_at?->format('Y-m-d H:i:s'),
                    $sale->order->user?->name ?? $sale->order->email,
                    $sale->product_title,
                    $sale->quantity,
                    format_price($sale->unit_price_cents),
                    format_price($sale->total_cents),
                    format_price($sale->seller_earnings_cents),
                    $sale->order->status,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="sales-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }
}