<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\Transaction;

class PaymentManager
{
    protected array $gateways = [];

    public function register(string $name, PaymentGatewayInterface $gateway): void
    {
        $this->gateways[$name] = $gateway;
    }

    public function get(string $name): ?PaymentGatewayInterface
    {
        return $this->gateways[$name] ?? null;
    }

    public function createSession(Order $order, string $gatewayName, array $options = []): array
    {
        $gateway = $this->get($gatewayName);
        if (!$gateway) {
            throw new \InvalidArgumentException("Payment gateway '$gatewayName' not registered");
        }

        $transaction = Transaction::create([
            'order_id' => $order->id,
            'gateway' => $gatewayName,
            'amount_cents' => $order->total_cents,
            'currency' => $order->currency,
            'status' => 'initiated',
        ]);

        try {
            $session = $gateway->createCheckoutSession($order, array_merge($options, ['transaction_id' => $transaction->id]));
            $transaction->update(['gateway_reference' => $session['reference'] ?? null]);
            return $session;
        } catch (\Throwable $e) {
            $transaction->update(['status' => 'failed', 'payload' => ['error' => $e->getMessage()]]);
            throw $e;
        }
    }

    public function handleCallback(string $gatewayName, array $payload): bool
    {
        $gateway = $this->get($gatewayName);
        if (!$gateway) {
            return false;
        }

        return $gateway->verifyPayment($payload);
    }

    public function processRefund(Transaction $transaction): bool
    {
        $gateway = $this->get($transaction->gateway);
        if (!$gateway) {
            return false;
        }

        $result = $gateway->refund($transaction->gateway_reference, $transaction->amount_cents);
        if ($result) {
            $transaction->update(['status' => 'refunded']);
        }
        return $result;
    }
}