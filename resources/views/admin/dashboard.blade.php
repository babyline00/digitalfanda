@extends('layouts.app')

@section('title', ' - Admin Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-dark">Admin Dashboard</h1>
            <p class="text-sm text-neutral-500 mt-1">Overview of your marketplace</p>
        </div>
        <div class="flex items-center gap-2 bg-white rounded-xl border border-neutral-200 px-4 py-2">
            <span class="w-2 h-2 rounded-full bg-secondary animate-pulse"></span>
            <span class="text-sm font-medium text-neutral-600">Live</span>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-2xl border border-neutral-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm font-medium text-neutral-500">Total Users</span>
                <span class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </span>
            </div>
            <p class="text-3xl font-bold text-dark">{{ number_format($stats['total_users']) }}</p>
            <p class="text-xs text-neutral-500 mt-1">{{ number_format($stats['total_sellers']) }} sellers</p>
        </div>

        <div class="bg-white rounded-2xl border border-neutral-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm font-medium text-neutral-500">Revenue</span>
                <span class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center text-green-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1c0 .288.104.607.182.976M12 17c-1.11 0-2.08-.402-2.599-1"></path></svg>
                </span>
            </div>
            <p class="text-3xl font-bold text-dark">{{ format_price($stats['total_revenue']) }}</p>
            <p class="text-xs text-neutral-500 mt-1">{{ format_price($stats['monthly_revenue']) }} this month</p>
        </div>

        <div class="bg-white rounded-2xl border border-neutral-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm font-medium text-neutral-500">Orders</span>
                <span class="w-10 h-10 rounded-xl bg-secondary/10 flex items-center justify-center text-secondary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </span>
            </div>
            <p class="text-3xl font-bold text-dark">{{ number_format($stats['total_orders']) }}</p>
            <p class="text-xs text-neutral-500 mt-1">{{ number_format($stats['paid_orders']) }} paid</p>
        </div>

        <div class="bg-white rounded-2xl border border-neutral-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm font-medium text-neutral-500">Pending Payouts</span>
                <span class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </span>
            </div>
            <p class="text-3xl font-bold text-dark">{{ format_price($stats['pending_payouts']) }}</p>
            <p class="text-xs text-neutral-500 mt-1">{{ format_price($stats['platform_fees']) }} platform fees</p>
        </div>
    </div>

    <!-- Moderation alerts -->
    @if($stats['pending_sellers'] > 0 || $stats['pending_products'] > 0)
        <div class="bg-amber-50 border border-amber-200 rounded-2xl px-6 py-4 mb-8 flex flex-col sm:flex-row sm:items-center gap-3">
            <span class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </span>
            <div class="flex-1">
                <p class="font-semibold text-amber-900">Items awaiting review</p>
                <p class="text-sm text-amber-700">{{ $stats['pending_sellers'] }} seller applications, {{ $stats['pending_products'] }} products pending</p>
            </div>
            <div class="flex gap-3">
                @if($stats['pending_sellers'] > 0)
                    <a href="{{ route('admin.sellers.index') }}" class="px-4 py-2 text-sm font-medium text-white bg-amber-600 rounded-lg hover:bg-amber-700 transition-colors">Review Sellers</a>
                @endif
                @if($stats['pending_products'] > 0)
                    <a href="{{ route('admin.products.index') }}" class="px-4 py-2 text-sm font-medium text-amber-700 border border-amber-300 rounded-lg hover:bg-amber-100 transition-colors">Review Products</a>
                @endif
            </div>
        </div>
    @endif

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-8">
        <div class="lg:col-span-2 bg-white rounded-2xl border border-neutral-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="font-bold text-dark">Revenue (last 30 days)</h2>
                <span class="text-xs font-medium text-neutral-500">Paid orders</span>
            </div>
            @if($revenueChart->isNotEmpty())
                <div class="flex items-end gap-1 h-48" aria-hidden="true">
                    @php
                        $maxRevenue = $revenueChart->max('revenue') ?: 1;
                    @endphp
                    @foreach($revenueChart as $point)
                        <div class="flex-1 flex flex-col items-center gap-2 group">
                            <div class="w-full rounded-t-lg bg-primary/20 group-hover:bg-primary transition-colors relative" style="height: {{ max(4, ($point->revenue / $maxRevenue) * 100) }}%">
                                <span class="absolute -top-7 left-1/2 -translate-x-1/2 text-[10px] font-medium text-neutral-600 whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity">{{ format_price($point->revenue) }}</span>
                            </div>
                            <span class="text-[10px] text-neutral-400">{{ \Carbon\Carbon::parse($point->date)->format('d') }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-neutral-500 py-16 text-center">No revenue data yet.</p>
            @endif
        </div>

        <div class="bg-white rounded-2xl border border-neutral-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="font-bold text-dark">New Sellers (30 days)</h2>
            </div>
            @if($sellerChart->isNotEmpty())
                <div class="space-y-3">
                    @foreach($sellerChart->reverse() as $point)
                        <div>
                            <div class="flex items-center justify-between text-xs text-neutral-500 mb-1">
                                <span>{{ \Carbon\Carbon::parse($point->date)->format('M d') }}</span>
                                <span class="font-medium text-dark">{{ $point->count }} new</span>
                            </div>
                            <div class="h-2 rounded-full bg-neutral-100 overflow-hidden">
                                <div class="h-full rounded-full bg-secondary" style="width: {{ min(100, $point->count * 20) }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-neutral-500 py-16 text-center">No seller signups yet.</p>
            @endif
        </div>
    </div>

    <!-- Recent activity -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 mb-8">
        <div class="bg-white rounded-2xl border border-neutral-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-neutral-200 flex items-center justify-between">
                <h2 class="font-bold text-dark">Recent Orders</h2>
                <a href="{{ route('admin.orders.index') }}" class="text-sm font-medium text-primary hover:text-primary-dark">View all</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs text-neutral-500 border-b border-neutral-100">
                            <th class="px-6 py-3 font-medium">Order</th>
                            <th class="px-6 py-3 font-medium">Customer</th>
                            <th class="px-6 py-3 font-medium">Total</th>
                            <th class="px-6 py-3 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @forelse($recentOrders as $order)
                            <tr>
                                <td class="px-6 py-3 font-mono text-xs">{{ $order->order_number }}</td>
                                <td class="px-6 py-3 text-neutral-600">{{ $order->user->name ?? '-' }}</td>
                                <td class="px-6 py-3 font-medium text-dark">{{ $order->getFormattedTotal() }}</td>
                                <td class="px-6 py-3">
                                    <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium
                                        {{ $order->status === 'paid' ? 'bg-green-100 text-green-700' : ($order->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-neutral-100 text-neutral-600') }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-neutral-500">No orders yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-neutral-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-neutral-200 flex items-center justify-between">
                <h2 class="font-bold text-dark">Recent Products</h2>
                <a href="{{ route('admin.products.index') }}" class="text-sm font-medium text-primary hover:text-primary-dark">View all</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs text-neutral-500 border-b border-neutral-100">
                            <th class="px-6 py-3 font-medium">Product</th>
                            <th class="px-6 py-3 font-medium">Seller</th>
                            <th class="px-6 py-3 font-medium">Price</th>
                            <th class="px-6 py-3 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @forelse($recentProducts as $product)
                            <tr>
                                <td class="px-6 py-3 font-medium text-dark max-w-[200px] truncate">{{ $product->name }}</td>
                                <td class="px-6 py-3 text-neutral-600">{{ $product->seller->store_name ?? '-' }}</td>
                                <td class="px-6 py-3 text-neutral-600">{{ $product->getFormattedPrice() }}</td>
                                <td class="px-6 py-3">
                                    <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium
                                        {{ $product->status === 'published' ? 'bg-green-100 text-green-700' : ($product->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-neutral-100 text-neutral-600') }}">
                                        {{ ucfirst($product->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-neutral-500">No products yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
        <div class="bg-white rounded-2xl border border-neutral-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-neutral-200 flex items-center justify-between">
                <h2 class="font-bold text-dark">New Sellers</h2>
                <a href="{{ route('admin.sellers.index') }}" class="text-sm font-medium text-primary hover:text-primary-dark">View all</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs text-neutral-500 border-b border-neutral-100">
                            <th class="px-6 py-3 font-medium">Store</th>
                            <th class="px-6 py-3 font-medium">Owner</th>
                            <th class="px-6 py-3 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @forelse($recentSellers as $seller)
                            <tr>
                                <td class="px-6 py-3 font-medium text-dark">{{ $seller->store_name }}</td>
                                <td class="px-6 py-3 text-neutral-600">{{ $seller->user->name ?? '-' }}</td>
                                <td class="px-6 py-3">
                                    <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium
                                        {{ $seller->status === 'approved' ? 'bg-green-100 text-green-700' : ($seller->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                                        {{ ucfirst($seller->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center text-neutral-500">No sellers yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-neutral-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-neutral-200 flex items-center justify-between">
                <h2 class="font-bold text-dark">Recent Payouts</h2>
                <a href="{{ route('admin.payouts.index') }}" class="text-sm font-medium text-primary hover:text-primary-dark">View all</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs text-neutral-500 border-b border-neutral-100">
                            <th class="px-6 py-3 font-medium">Seller</th>
                            <th class="px-6 py-3 font-medium">Amount</th>
                            <th class="px-6 py-3 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @forelse($recentPayouts as $payout)
                            <tr>
                                <td class="px-6 py-3 font-medium text-dark">{{ $payout->seller->store_name ?? '-' }}</td>
                                <td class="px-6 py-3 text-neutral-600">{{ format_price($payout->amount_cents) }}</td>
                                <td class="px-6 py-3">
                                    <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium
                                        {{ $payout->status === 'paid' ? 'bg-green-100 text-green-700' : ($payout->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-neutral-100 text-neutral-600') }}">
                                        {{ ucfirst($payout->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center text-neutral-500">No payouts yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection