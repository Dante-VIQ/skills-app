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
Schema::create('child_impact_contributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('project_id')->constrained('social_impact_projects')->onDelete('cascade');
            $table->decimal('amount_generated', 10, 2);
            $table->integer('impact_points')->default(0);
            $table->string('contribution_type');
            $table->string('activity_source')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['child_id', 'project_id']);
            $table->index('contribution_type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('child_impact_contributions');
    }
};
