<?php

namespace App\Services\Payment\Gateways;

use App\Models\Order;
use App\Services\Payment\PaymentGatewayInterface;

class StripeGateway implements PaymentGatewayInterface
{
    public function getName(): string
    {
        return 'stripe';
    }

    public function createCheckoutSession(Order $order, array $options = []): array
    {
        // In production, use Stripe SDK:
        // $session = \Stripe\Checkout\Session::create([...]);
        
        return [
            'url' => route('checkout.stripe.redirect', ['order' => $order->number]) . '?session_id=cs_test_' . uniqid(),
            'reference' => 'cs_test_' . uniqid(),
            'requires_redirect' => true,
        ];
    }

    public function verifyPayment(array $payload): bool
    {
        // Verify Stripe webhook signature
        // $event = \Stripe\Webhook::constructEvent(...);
        // return $event->type === 'checkout.session.completed';
        
        // For development, always return true for testing
        return true;
    }

    public function refund(string $gatewayReference, int $amountCents): bool
    {
        // $refund = \Stripe\Refund::create(['payment_intent' => $gatewayReference, 'amount' => $amountCents]);
        // return $refund->status === 'succeeded';
        return true;
    }
}