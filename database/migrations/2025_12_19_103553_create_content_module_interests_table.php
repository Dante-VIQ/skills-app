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
 Schema::create('content_module_interests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_module_id')->constrained('content_modules')->onDelete('cascade');
            $table->foreignId('interest_id')->constrained('interests')->onDelete('cascade');
            $table->integer('relevance_score')->default(1);
            $table->timestamps();
            
            $table->unique(['content_module_id', 'interest_id']);
            $table->index('relevance_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_module_interests');
    }
};
