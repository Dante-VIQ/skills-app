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
        Schema::create('activity_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('content_module_id')->constrained('content_modules')->onDelete('cascade');
            $table->decimal('relevance_score', 5, 4);
            $table->json('match_reasons');
            $table->boolean('shown')->default(false);
            $table->boolean('engaged')->default(false);
            $table->timestamp('shown_at')->nullable();
            $table->timestamps();
            
            $table->unique(['child_id', 'content_module_id']);
            $table->index(['child_id', 'relevance_score']);
            $table->index(['shown', 'engaged']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_recommendations');
    }
};
