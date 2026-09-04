<?php

namespace App\Providers;

use App\Services\CartService;
use App\Services\DownloadService;
use App\Services\OrderService;
use App\Services\PayoutService;
use App\Services\Payment\PaymentManager;
use App\Services\Payment\Gateways\BankTransferGateway;
use App\Services\Payment\Gateways\CashGateway;
use App\Services\Payment\Gateways\FreeGateway;
use App\Services\Payment\Gateways\ManualGateway;
use App\Services\Payment\Gateways\PayPalGateway;
use App\Services\Payment\Gateways\RazorpayGateway;
use App\Services\Payment\Gateways\StripeGateway;
use Illuminate\Support\ServiceProvider;

class MarketplaceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CartService::class, fn() => new CartService());
        $this->app->singleton(DownloadService::class, fn() => new DownloadService());
        $this->app->singleton(PayoutService::class, fn() => new PayoutService());
        $this->app->singleton(OrderService::class, fn($app) => new OrderService($app->make(PaymentManager::class)));

        $this->app->singleton(PaymentManager::class, function ($app) {
            $manager = new PaymentManager();
            $manager->register('stripe', new StripeGateway());
            $manager->register('paypal', new PayPalGateway());
            $manager->register('razorpay', new RazorpayGateway());
            $manager->register('cash', new CashGateway());
            $manager->register('free', new FreeGateway());
            $manager->register('manual', new ManualGateway());
            $manager->register('bank_transfer', new BankTransferGateway());
            return $manager;
        });
    }

    public function boot(): void
    {
        //
    }
}