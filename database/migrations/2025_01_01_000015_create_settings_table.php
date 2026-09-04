<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('group')->index();
            $table->string('key');
            $table->text('value')->nullable();
            $table->string('type')->default('string');
            $table->string('label')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();

            $table->unique(['group', 'key']);
        });

        $defaults = [
            ['group' => 'general', 'key' => 'site_name', 'value' => 'DigitalFanda', 'type' => 'string', 'label' => 'Site Name'],
            ['group' => 'general', 'key' => 'site_description', 'value' => 'Digital products marketplace', 'type' => 'string', 'label' => 'Site Description'],
            ['group' => 'general', 'key' => 'default_currency', 'value' => 'USD', 'type' => 'string', 'label' => 'Default Currency'],
            ['group' => 'general', 'key' => 'maintenance_mode', 'value' => 'false', 'type' => 'bool', 'label' => 'Maintenance Mode'],

            ['group' => 'commissions', 'key' => 'platform_commission_rate', 'value' => '10.00', 'type' => 'string', 'label' => 'Platform Commission Rate (%)'],
            ['group' => 'commissions', 'key' => 'seller_min_payout_cents', 'value' => '5000', 'type' => 'int', 'label' => 'Seller Minimum Payout ($50.00)'],

            ['group' => 'payments', 'key' => 'stripe_enabled', 'value' => 'false', 'type' => 'bool', 'label' => 'Stripe Enabled'],
            ['group' => 'payments', 'key' => 'paypal_enabled', 'value' => 'false', 'type' => 'bool', 'label' => 'PayPal Enabled'],
            ['group' => 'payments', 'key' => 'razorpay_enabled', 'value' => 'false', 'type' => 'bool', 'label' => 'Razorpay Enabled'],

            ['group' => 'emails', 'key' => 'from_name', 'value' => 'DigitalFanda', 'type' => 'string', 'label' => 'From Name'],
            ['group' => 'emails', 'key' => 'from_address', 'value' => 'noreply@digitalfanda.test', 'type' => 'string', 'label' => 'From Address'],
            ['group' => 'emails', 'key' => 'order_confirmation_enabled', 'value' => 'true', 'type' => 'bool', 'label' => 'Order Confirmation Email'],
            ['group' => 'emails', 'key' => 'download_ready_enabled', 'value' => 'true', 'type' => 'bool', 'label' => 'Download Ready Email'],

            ['group' => 'appearance', 'key' => 'primary_color', 'value' => '#6C5CE7', 'type' => 'string', 'label' => 'Primary Color'],
            ['group' => 'appearance', 'key' => 'secondary_color', 'value' => '#00D9A3', 'type' => 'string', 'label' => 'Secondary Color'],
        ];

        foreach ($defaults as $setting) {
            DB::table('settings')->insert($setting);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};