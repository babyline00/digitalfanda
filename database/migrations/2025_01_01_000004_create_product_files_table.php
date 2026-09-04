<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // label like "Main PDF", "Video 1080p"
            $table->string('disk')->default('local');
            $table->string('path');
            $table->bigInteger('size_bytes');
            $table->string('mime');
            $table->string('version')->nullable();
            $table->unsignedInteger('max_downloads')->nullable(); // null = unlimited
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_files');
    }
};