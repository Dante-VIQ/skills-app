<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('watch_time_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('content_module_id')->constrained('content_modules')->onDelete('cascade');
            $table->foreignId('content_category_id')->constrained('content_categories')->onDelete('cascade');
            $table->integer('duration'); // in minutes
            $table->integer('progress_percentage')->default(0); // 0-100
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->json('watch_points')->nullable(); // Timestamps of key moments watched
            $table->string('device_type')->nullable(); // mobile, desktop, tablet
            $table->string('session_id')->nullable();
            $table->timestamps();
            
            $table->index(['child_id', 'created_at']);
            $table->index(['content_category_id', 'child_id']);
            $table->index('content_module_id');
            $table->index('completed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('watch_time_records');
    }
};