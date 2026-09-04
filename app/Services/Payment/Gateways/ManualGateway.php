<?php

namespace App\Services\Payment\Gateways;

use App\Models\Order;
use App\Services\Payment\PaymentGatewayInterface;

class ManualGateway implements PaymentGatewayInterface
{
    public function getName(): string
    {
        return 'manual';
    }

    public function createCheckoutSession(Order $order, array $options = []): array
    {
        return [
            'url' => route('admin.orders.show', $order->id) . '?action=mark_paid',
            'reference' => 'MANUAL-' . $order->number,
            'requires_redirect' => false,
        ];
    }

    public function verifyPayment(array $payload): bool
    {
        return isset($payload['confirmed_by_admin']) && $payload['confirmed_by_admin'] === true;
    }

    public function refund(string $gatewayReference, int $amountCents): bool
    {
        return true;
    }
}