<?php

namespace App\Services\Payment\Gateways;

use App\Models\Order;
use App\Services\Payment\PaymentGatewayInterface;

class CashGateway implements PaymentGatewayInterface
{
    public function getName(): string
    {
        return 'cash';
    }

    public function createCheckoutSession(Order $order, array $options = []): array
    {
        return [
            'url' => null,
            'reference' => 'CASH-' . $order->number,
            'requires_redirect' => false,
        ];
    }

    public function verifyPayment(array $payload): bool
    {
        return true; // Cash is immediate
    }

    public function refund(string $gatewayReference, int $amountCents): bool
    {
        return true;
    }
}