<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('skill_id')->constrained('skills')->onDelete('cascade');
            $table->float('progress')->default(0); // 0-100
            $table->integer('modules_completed')->default(0);
            $table->integer('total_modules')->default(0);
            $table->timestamp('last_practiced_at')->nullable();
            $table->string('level')->default('beginner'); // beginner, intermediate, advanced, expert
            $table->json('milestones')->nullable(); // Achieved milestones
            $table->timestamps();
            
            $table->unique(['user_id', 'skill_id'], 'user_skill_unique');
            $table->index(['user_id', 'progress']);
            $table->index('skill_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_skills');
    }
};