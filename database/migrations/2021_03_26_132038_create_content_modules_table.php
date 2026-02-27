<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_modules', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->foreignId('content_category_id')->constrained('content_categories')->onDelete('cascade');
            $table->foreignId('series_id')->nullable()->constrained('content_series')->onDelete('set null');
            $table->integer('order')->default(0); // Order within series
            $table->string('content_type'); // video, article, quiz, interactive, game
            $table->integer('duration')->nullable(); // in minutes
            $table->string('difficulty')->default('beginner'); // beginner, intermediate, advanced
            $table->integer('min_age')->nullable();
            $table->integer('max_age')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('thumbnail')->nullable();
            $table->string('content_url')->nullable(); // External URL
            $table->text('embedded_content')->nullable(); // Embedded code (YouTube, Vimeo, etc.)
            $table->json('metadata')->nullable(); // Additional metadata for ML
            $table->integer('views_count')->default(0);
            $table->integer('likes_count')->default(0);
            $table->integer('completion_count')->default(0);
            $table->float('average_rating')->default(0);
            $table->timestamps();
            
            $table->index(['content_category_id', 'difficulty']);
            $table->index(['series_id', 'order']);
            $table->index(['min_age', 'max_age']);
            $table->index('is_active');
            $table->index('views_count');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_modules');
    }
};