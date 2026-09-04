<?php

namespace App\Services\Payment\Gateways;

use App\Models\Order;
use App\Services\Payment\PaymentGatewayInterface;

class BankTransferGateway implements PaymentGatewayInterface
{
    public function getName(): string
    {
        return 'bank_transfer';
    }

    public function createCheckoutSession(Order $order, array $options = []): array
    {
        $bankDetails = setting('payments.bank_transfer_details', [
            'account_name' => 'DigitalFanda',
            'account_number' => '1234567890',
            'routing_number' => '021000021',
            'bank_name' => 'Example Bank',
            'reference' => $order->number,
        ]);

        return [
            'url' => null,
            'reference' => 'BT-' . $order->number,
            'requires_redirect' => false,
            'instructions' => $bankDetails,
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