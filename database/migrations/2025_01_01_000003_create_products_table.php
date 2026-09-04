<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('subtitle')->nullable();
            $table->longText('description')->nullable();
            $table->enum('type', ['download', 'stream', 'course', 'license_key'])->default('download');
            $table->enum('status', ['draft', 'pending', 'published', 'rejected', 'archived'])->default('draft')->index();
            $table->text('moderation_note')->nullable();
            $table->bigInteger('price_cents')->unsigned();
            $table->bigInteger('compare_at_price_cents')->nullable();
            $table->char('currency', 3)->default('USD');
            $table->boolean('pay_what_you_want')->default(false);
            $table->bigInteger('min_pwyw_cents')->nullable();
            $table->string('license_key_prefix')->nullable();
            $table->string('thumbnail_path')->nullable();
            $table->string('preview_url')->nullable();
            $table->unsignedInteger('sales_count')->default(0);
            $table->unsignedInteger('views_count')->default(0);
            $table->decimal('rating_avg', 3, 2)->default(0);
            $table->unsignedInteger('rating_count')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['seller_id', 'status']);
            $table->index(['category_id', 'status']);
            $table->index(['status', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};