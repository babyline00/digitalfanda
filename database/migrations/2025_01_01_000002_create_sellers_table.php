<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sellers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('store_name');
            $table->string('slug')->unique();
            $table->text('bio')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('banner_path')->nullable();
            $table->string('website')->nullable();
            $table->enum('status', ['pending', 'approved', 'suspended', 'rejected'])->default('pending')->index();
            $table->decimal('commission_rate', 5, 2)->nullable(); // overrides platform default
            $table->string('payout_email')->nullable();
            $table->enum('payout_method', ['paypal', 'bank', 'stripe_connect'])->default('paypal');
            $table->json('payout_details')->nullable();
            $table->enum('kyc_status', ['unverified', 'pending', 'verified'])->default('unverified');
            $table->bigInteger('balance_cents')->default(0);
            $table->bigInteger('total_sales_cents')->default(0);
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('suspended_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sellers');
    }
};