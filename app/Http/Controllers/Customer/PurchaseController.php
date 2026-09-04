<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Auth::user()->orders()
            ->with(['items.product.seller', 'transactions'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(15)->withQueryString();

        return view('customer.purchases', compact('orders'));
    }

    public function show(Order $order)
    {
        $this->authorize('view', $order);

        $order->load(['items.product.seller', 'items.downloads.productFile', 'transactions', 'coupon']);

        return view('customer.purchases.show', compact('order'));
    }

    public function download(Order $order, $itemId)
    {
        $this->authorize('view', $order);

        $item = $order->items()->findOrFail($itemId);

        $download = $item->downloads()->first();

        if (!$download || !$download->isValid()) {
            abort(403, 'Download not available or expired.');
        }

        return redirect()->route('download.file', $download->token);
    }

    public function requestRefund(Request $request, Order $order)
    {
        $this->authorize('view', $order);

        abort_unless($order->canBeRefunded(), 403, 'This order cannot be refunded.');

        $request->validate(['reason' => 'required|string|max:1000']);

        // Create refund request - could be a separate model
        $order->update([
            'notes' => ($order->notes ?? '') . "\nRefund requested: {$request->reason}",
        ]);

        // Notify admin
        // Notification::send(...)

        return back()->with('success', 'Refund request submitted. Our team will review it.');
    }
}