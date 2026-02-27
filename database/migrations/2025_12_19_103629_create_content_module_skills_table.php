<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_module_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_module_id')->constrained('content_modules')->onDelete('cascade');
            $table->foreignId('skill_id')->constrained('skills')->onDelete('cascade');
            $table->float('weight')->default(1.0); // How much this module contributes to the skill
            $table->timestamps();
            
            $table->unique(['content_module_id', 'skill_id'], 'module_skill_unique');
            $table->index('skill_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_module_skills');
    }
};