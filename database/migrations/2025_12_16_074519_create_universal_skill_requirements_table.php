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
    Schema::create('universal_skill_requirements', function (Blueprint $table) {
        $table->id();
        $table->foreignId('parent_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('child_id')->constrained('users')->onDelete('cascade');
        
        // Make sure this matches the content_categories.id type
        $table->unsignedBigInteger('category_id');
        
        $table->integer('required_minutes_per_week');
        $table->boolean('is_active')->default(true);
        $table->boolean('blocks_content')->default(true);
        $table->timestamps();
        
        // Add unique constraint
        $table->unique(['child_id', 'category_id']);
        
        // Add foreign key constraint AFTER table creation
         $table->engine = 'InnoDB';
    });
    
    // Add foreign key in a separate statement
    Schema::table('universal_skill_requirements', function (Blueprint $table) {
        $table->foreign('category_id')
              ->references('id')
              ->on('content_categories')
              ->onDelete('cascade');
    });

   
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('universal_skill_requirements');
    }
};
