<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Seller;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Payment\PaymentManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    protected PaymentManager $paymentManager;

    public function __construct(PaymentManager $paymentManager)
    {
        $this->paymentManager = $paymentManager;
    }

    public function createFromCart(array $cart, User|string|null $user, string $email, array $options = []): Order
    {
        return DB::transaction(function () use ($cart, $user, $email, $options) {
            $subtotal = 0;
            $platformFee = 0;
            $sellerEarnings = [];

            foreach ($cart as $item) {
                $product = Product::with('seller')->findOrFail($item['product_id']);
                $qty = $item['quantity'] ?? 1;
                $unitPrice = $product->price_cents;
                $lineTotal = $unitPrice * $qty;
                $subtotal += $lineTotal;

                $rate = $product->seller->getEffectiveCommissionRate() / 100;
                $fee = (int) round($lineTotal * $rate);
                $platformFee += $fee;
                $sellerEarnings[$product->seller_id] = ($sellerEarnings[$product->seller_id] ?? 0) + ($lineTotal - $fee);
            }

            $discount = 0;
            $coupon = null;
            if (!empty($options['coupon_code'])) {
                $coupon = Coupon::where('code', $options['coupon_code'])->first();
                if ($coupon && $coupon->isValid($subtotal)) {
                    $discount = $coupon->calculateDiscount($subtotal);
                    $coupon->increment('used_count');
                }
            }

            $tax = 0; // Tax calculation would go here
            $total = $subtotal - $discount + $tax + $platformFee;

            $order = Order::create([
                'number' => generate_order_number(),
                'user_id' => $user instanceof User ? $user->id : null,
                'email' => $email,
                'status' => 'pending',
                'source' => $options['source'] ?? 'web',
                'subtotal_cents' => $subtotal,
                'discount_cents' => $discount,
                'tax_cents' => $tax,
                'fee_cents' => $platformFee,
                'total_cents' => $total,
                'coupon_id' => $coupon?->id,
                'currency' => $options['currency'] ?? 'USD',
                'ip_address' => request()?->ip(),
                'pos_register_id' => $options['pos_register_id'] ?? null,
                'cashier_id' => $options['cashier_id'] ?? null,
                'billing_info' => $options['billing_info'] ?? null,
            ]);

            foreach ($cart as $item) {
                $product = Product::with('seller')->findOrFail($item['product_id']);
                $qty = $item['quantity'] ?? 1;
                $unitPrice = $product->price_cents;
                $lineTotal = $unitPrice * $qty;
                $rate = $product->seller->getEffectiveCommissionRate() / 100;
                $fee = (int) round($lineTotal * $rate);

                $licenseKey = null;
                if ($product->type === 'license_key' && $product->license_key_prefix) {
                    $licenseKey = generate_license_key($product->license_key_prefix);
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'seller_id' => $product->seller_id,
                    'product_title' => $product->title,
                    'variant_name' => $item['variant_name'] ?? null,
                    'unit_price_cents' => $unitPrice,
                    'quantity' => $qty,
                    'discount_cents' => 0,
                    'total_cents' => $lineTotal,
                    'platform_fee_cents' => $fee,
                    'seller_earnings_cents' => $lineTotal - $fee,
                    'license_key' => $licenseKey,
                ]);

                $product->increment('sales_count', $qty);
                $product->seller->increment('total_sales_cents', $lineTotal - $fee);
            }

            if ($total <= 0) {
                $this->markPaid($order, 'free');
            }

            return $order->load('items.product', 'items.seller');
        });
    }

    public function markPaid(Order $order, string $gateway, string $gatewayReference = null): void
    {
        DB::transaction(function () use ($order, $gateway, $gatewayReference) {
            $order->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            Transaction::create([
                'order_id' => $order->id,
                'gateway' => $gateway,
                'gateway_reference' => $gatewayReference ?? ($gateway . '-' . $order->number),
                'amount_cents' => $order->total_cents,
                'currency' => $order->currency,
                'status' => 'succeeded',
                'processed_at' => now(),
            ]);

            $this->grantDownloads($order);
            $this->distributeEarnings($order);
            $this->handleAffiliateCommission($order);
        });
    }

    protected function grantDownloads(Order $order): void
    {
        foreach ($order->items as $item) {
            if (!$item->product->files()->exists()) {
                continue;
            }

            foreach ($item->product->files as $file) {
                $maxDownloads = $file->max_downloads ?? setting('general.default_max_downloads', 5);

                Download::create([
                    'order_item_id' => $item->id,
                    'product_file_id' => $file->id,
                    'token' => (string) Str::uuid(),
                    'expires_at' => now()->addDays(setting('general.download_expiry_days', 30)),
                    'max_downloads' => $maxDownloads,
                ]);
            }

            if ($item->product->type === 'course') {
                // Course access granted via order_items check in frontend
            }
        }
    }

    protected function distributeEarnings(Order $order): void
    {
        foreach ($order->items as $item) {
            $seller = $item->seller;
            $seller->increment('balance_cents', $item->seller_earnings_cents);
        }
    }

    protected function handleAffiliateCommission(Order $order): void
    {
        // Check if order came from affiliate link (stored in session/cookie)
        $affiliateCode = session('affiliate_code');
        if (!$affiliateCode) {
            return;
        }

        $affiliate = \App\Models\Affiliate::where('code', $affiliateCode)->first();
        if (!$affiliate) {
            return;
        }

        $rate = $affiliate->commission_rate ?: setting('commissions.affiliate_default_rate', 5.0) / 100;
        $commission = (int) round($order->subtotal_cents * $rate);

        \App\Models\AffiliateConversion::create([
            'affiliate_id' => $affiliate->id,
            'order_id' => $order->id,
            'commission_cents' => $commission,
            'status' => 'pending',
        ]);

        $affiliate->increment('conversions');
        $affiliate->addEarnings($commission);
    }

    public function refund(Order $order, string $reason = ''): void
    {
        DB::transaction(function () use ($order, $reason) {
            $order->update([
                'status' => 'refunded',
                'refunded_at' => now(),
                'notes' => $order->notes . "\nRefund: $reason",
            ]);

            foreach ($order->transactions()->where('status', 'succeeded')->get() as $txn) {
                $this->paymentManager->processRefund($txn);
            }

            // Revoke downloads
            foreach ($order->items as $item) {
                $item->downloads()->update(['max_downloads' => 0]);
            }

            // Deduct seller earnings
            foreach ($order->items as $item) {
                $item->seller->decrement('balance_cents', $item->seller_earnings_cents);
                $item->seller->decrement('total_sales_cents', $item->seller_earnings_cents);
            }
        });
    }
}