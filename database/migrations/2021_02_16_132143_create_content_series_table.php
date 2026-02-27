<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_series', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('content_categories')->onDelete('set null');
            $table->integer('total_modules')->default(0);
            $table->integer('total_duration')->default(0); // Total minutes
            $table->string('difficulty')->default('beginner'); // beginner, intermediate, advanced
            $table->integer('min_age')->nullable();
            $table->integer('max_age')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['category_id', 'difficulty']);
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_series');
    }
};