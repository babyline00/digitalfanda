<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->enum('gateway', ['stripe', 'paypal', 'razorpay', 'cash', 'free', 'manual', 'bank_transfer']);
            $table->string('gateway_reference')->nullable()->index();
            $table->bigInteger('amount_cents');
            $table->char('currency', 3)->default('USD');
            $table->enum('status', ['initiated', 'succeeded', 'failed', 'refunded', 'pending'])->default('initiated');
            $table->json('payload')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};