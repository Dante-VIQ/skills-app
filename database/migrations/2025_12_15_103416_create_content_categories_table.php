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
        Schema::create('content_categories', function (Blueprint $table) {
            $table->id(); // This creates BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_universal')->default(false);
            $table->boolean('parent_enforced')->default(false);
            $table->integer('required_watch_minutes')->default(0);
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
            $table->timestamps();
            
            // Add index for faster queries
            $table->index('is_universal');
            $table->index('parent_enforced');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_categories');
    }
};
