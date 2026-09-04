<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique(); // DF-XXXXXX
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('email');
            $table->enum('status', ['pending', 'paid', 'failed', 'refunded', 'partially_refunded', 'cancelled'])->default('pending')->index();
            $table->enum('source', ['web', 'pos'])->default('web');
            $table->bigInteger('subtotal_cents');
            $table->bigInteger('discount_cents')->default(0);
            $table->bigInteger('tax_cents')->default(0);
            $table->bigInteger('fee_cents')->default(0); // platform commission total
            $table->bigInteger('total_cents');
            $table->foreignId('coupon_id')->nullable()->constrained()->nullOnDelete();
            $table->char('currency', 3)->default('USD');
            $table->string('ip_address')->nullable();
            $table->foreignId('pos_register_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('cashier_id')->nullable()->constrained('users')->nullOnDelete();
            $table->json('billing_info')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['status', 'created_at']);
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->foreignId('seller_id')->constrained()->cascadeOnDelete();
            $table->string('product_title');
            $table->string('variant_name')->nullable();
            $table->bigInteger('unit_price_cents');
            $table->unsignedSmallInteger('quantity')->default(1);
            $table->bigInteger('discount_cents')->default(0);
            $table->bigInteger('total_cents');
            $table->bigInteger('platform_fee_cents')->default(0);
            $table->bigInteger('seller_earnings_cents')->default(0);
            $table->string('license_key')->nullable()->unique();
            $table->timestamps();

            $table->index(['seller_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};