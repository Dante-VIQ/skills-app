<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
 Schema::create('content_modules', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['video', 'article', 'interactive', 'quiz', 'project']);
            $table->string('url');
            $table->string('thumbnail')->nullable();
            $table->integer('duration_minutes')->default(5);
            $table->integer('difficulty_level')->default(1);
            $table->integer('age_min')->default(0);
            $table->integer('age_max')->default(18);
            $table->foreignId('category_id')->nullable()->constrained('content_categories')->onDelete('set null');
            $table->string('source')->default('youtube');
            $table->string('external_id')->nullable();
            $table->json('metadata')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['type', 'is_active']);
            $table->index(['age_min', 'age_max']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_modules');
    }
};
