<?php

namespace App\Services\Payment;

use App\Models\Order;

interface PaymentGatewayInterface
{
    public function getName(): string;

    public function createCheckoutSession(Order $order, array $options = []): array;

    public function verifyPayment(array $payload): bool;

    public function refund(string $gatewayReference, int $amountCents): bool;
}