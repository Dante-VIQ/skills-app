<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('impact_projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('category')->default('education'); // education, health, environment, community
            $table->string('image_url')->nullable();

            $table->decimal('goal_amount', 12, 2)->default(0);
            $table->decimal('raised_amount', 12, 2)->default(0);
            $table->integer('goal_hours')->default(0);
            $table->integer('contributed_hours')->default(0);

            $table->string('status')->default('active'); // active, completed, paused
            $table->boolean('is_featured')->default(false);

            $table->timestamps();

            $table->index('status');
            $table->index('category');
            $table->index('is_featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('impact_projects');
    }
};