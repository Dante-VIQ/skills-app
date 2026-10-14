<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tutor_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tutor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('parent_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('child_id')->nullable()->constrained('users')->nullOnDelete();

            $table->integer('rating');
            $table->text('review')->nullable();
            $table->json('strengths')->nullable();
            $table->json('areas_for_improvement')->nullable();

            $table->boolean('is_verified')->default(true);
            $table->boolean('is_public')->default(true);
            $table->boolean('is_featured')->default(false);

            $table->timestamps();

            $table->index(['tutor_id', 'rating']);
            $table->index(['parent_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tutor_reviews');
    }
};