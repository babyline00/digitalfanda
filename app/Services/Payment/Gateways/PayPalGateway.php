<?php

namespace App\Services\Payment\Gateways;

use App\Models\Order;
use App\Services\Payment\PaymentGatewayInterface;

class PayPalGateway implements PaymentGatewayInterface
{
    public function getName(): string
    {
        return 'paypal';
    }

    public function createCheckoutSession(Order $order, array $options = []): array
    {
        // In production, use PayPal SDK
        return [
            'url' => route('checkout.paypal.redirect', ['order' => $order->number]) . '?token=EC-' . uniqid(),
            'reference' => 'EC-' . uniqid(),
            'requires_redirect' => true,
        ];
    }

    public function verifyPayment(array $payload): bool
    {
        // Verify PayPal webhook
        return true;
    }

    public function refund(string $gatewayReference, int $amountCents): bool
    {
        return true;
    }
}