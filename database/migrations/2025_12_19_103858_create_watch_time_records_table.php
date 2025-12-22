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
        Schema::create('watch_time_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('content_id')->constrained('content_modules')->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained('content_categories')->onDelete('set null');
            $table->integer('minutes_watched');
            $table->date('watch_date');
            $table->decimal('engagement_score', 3, 2)->default(1.0);
            $table->timestamps();
            
            $table->unique(['child_id', 'content_id', 'watch_date']);
            $table->index(['child_id', 'category_id', 'watch_date']);
            $table->index('watch_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('watch_time_records');
    }
};
