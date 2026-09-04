<?php

namespace App\Services\Payment\Gateways;

use App\Models\Order;
use App\Services\Payment\PaymentGatewayInterface;

class FreeGateway implements PaymentGatewayInterface
{
    public function getName(): string
    {
        return 'free';
    }

    public function createCheckoutSession(Order $order, array $options = []): array
    {
        if ($order->total_cents > 0) {
            throw new \InvalidArgumentException('Free gateway only for zero-amount orders');
        }
        return [
            'url' => null,
            'reference' => 'FREE-' . $order->number,
            'requires_redirect' => false,
        ];
    }

    public function verifyPayment(array $payload): bool
    {
        return true;
    }

    public function refund(string $gatewayReference, int $amountCents): bool
    {
        return true;
    }
}