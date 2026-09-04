<?php

namespace App\Services\Payment\Gateways;

use App\Models\Order;
use App\Services\Payment\PaymentGatewayInterface;

class RazorpayGateway implements PaymentGatewayInterface
{
    public function getName(): string
    {
        return 'razorpay';
    }

    public function createCheckoutSession(Order $order, array $options = []): array
    {
        // In production, use Razorpay SDK
        return [
            'url' => route('checkout.razorpay.redirect', ['order' => $order->number]) . '?order_id=order_' . uniqid(),
            'reference' => 'order_' . uniqid(),
            'requires_redirect' => true,
        ];
    }

    public function verifyPayment(array $payload): bool
    {
        // Verify Razorpay webhook signature
        return true;
    }

    public function refund(string $gatewayReference, int $amountCents): bool
    {
        return true;
    }
}