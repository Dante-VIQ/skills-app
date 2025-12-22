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
    Schema::create('social_impact_projects', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->enum('type', ['school', 'lab', 'library', 'community_center', 'scholarship']);
        $table->text('description');
        $table->string('location');
        $table->decimal('target_amount', 10, 2);
        $table->decimal('amount_raised', 10, 2)->default(0);
        $table->integer('children_impacted')->default(0);
        $table->json('progress_photos')->nullable();
        $table->timestamp('completion_date')->nullable();
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_impact_ecosystem');
    }
};
