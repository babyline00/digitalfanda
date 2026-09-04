<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('content')->nullable(); // text/markdown content or embed code
            $table->string('video_url')->nullable();
            $table->unsignedInteger('duration_min')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->unsignedInteger('available_after_days')->default(0); // drip content
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};