<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable(); // Font Awesome icon class
            $table->string('color')->default('blue'); // Theme color
            $table->boolean('is_active')->default(true);
            $table->integer('min_age')->nullable(); // Minimum age appropriateness
            $table->integer('max_age')->nullable(); // Maximum age appropriateness
            $table->foreignId('parent_id')->nullable()->constrained('content_categories')->onDelete('cascade');
            $table->integer('display_order')->default(0);
            $table->json('metadata')->nullable(); // Additional category data
            $table->timestamps();
            
            $table->index(['is_active', 'parent_id']);
            $table->index('display_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_categories');
    }
};