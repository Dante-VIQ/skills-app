<?php
// database/migrations/2024_01_01_000001_create_learning_resources_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // database/migrations/2024_01_xx_create_learning_resources_table.php
        Schema::create('learning_resources', function ($table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('summary')->nullable(); // Our original summary

            // Content typing
            $table->enum('content_type', ['video', 'article', 'interactive', 'quiz', 'robotics_project']);
            $table->enum('source_type', ['youtube', 'khan_academy', 'w3schools', 'original', 'robotics_kit']);

            // Source tracking (for external content)
            $table->string('external_url')->nullable();
            $table->string('source_id')->nullable(); // YouTube video ID, etc.
            $table->json('source_metadata')->nullable(); // Thumbnails, duration, etc.
            $table->string('attribution')->nullable(); // "Via Khan Academy"

            // Our original content
            $table->longText('original_content')->nullable(); // Our explanations
            $table->json('learning_objectives')->nullable();
            $table->json('prerequisites')->nullable();

            // Robotics specific
            $table->string('difficulty_level')->nullable(); // Beginner/Intermediate/Advanced
            $table->json('materials_needed')->nullable(); // For robotics projects
            $table->json('code_samples')->nullable(); // Embedded code blocks

            // Engagement
            $table->integer('estimated_minutes');
            $table->integer('points')->default(10);
            $table->json('tags')->nullable();

            $table->foreignId('content_category_id')->constrained();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->boolean('is_published')->default(false);
            $table->boolean('is_free')->default(true);
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_resources');
    }
};
