<?php
// database/migrations/2026_02_26_150337_create_user_interactions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Disable foreign key checks temporarily
        Schema::disableForeignKeyConstraints();

        Schema::create('user_interactions', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            
            $table->id();
            
            // Foreign keys
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade');
                  
            $table->foreignId('learning_resource_id')
                  ->constrained()
                  ->onDelete('cascade');
            
            // Interaction type
            $table->string('interaction_type', 50)->index('idx_interaction_type');
            
            // Metrics
            $table->unsignedInteger('watch_duration')->nullable();
            $table->unsignedTinyInteger('watch_percentage')->nullable();
            $table->boolean('completed')->default(false)->index('idx_completed');
            
            // Feedback
            $table->unsignedTinyInteger('rating')->nullable();
            $table->text('feedback')->nullable();
            $table->json('quiz_results')->nullable();
            
            // Context
            $table->string('session_id', 100)->nullable()->index('idx_session');
            $table->string('device_type', 20)->nullable();
            $table->string('browser', 50)->nullable();
            $table->string('platform', 20)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            
            // Content position
            $table->unsignedInteger('position_seconds')->nullable();
            $table->string('section', 100)->nullable();
            
            // Marketing
            $table->string('utm_source', 100)->nullable();
            $table->string('utm_medium', 50)->nullable();
            $table->string('utm_campaign', 100)->nullable();
            $table->string('referrer_url')->nullable();
            
            // ML scores
            $table->float('engagement_score', 8, 2)->nullable();
            $table->float('interest_score', 8, 2)->nullable();
            $table->json('ai_insights')->nullable();
            
            // Interaction date
            $table->dateTime('interaction_date')
                  ->nullable()
                  ->default(DB::raw('CURRENT_TIMESTAMP'))
                  ->index('idx_interaction_date');
            
            // Laravel timestamps
            $table->timestamps();
            
            // ============= FIXED: SHORT INDEX NAMES =============
            
            // Single column indexes with short names
            $table->index('user_id', 'idx_user');
            $table->index('learning_resource_id', 'idx_resource');
            
            // Composite indexes with SHORT custom names
            $table->index(['user_id', 'interaction_type'], 'idx_user_type');
            $table->index(['user_id', 'interaction_type', 'created_at'], 'idx_user_type_time');
            
            // FIXED: This was the problematic long name
            // FROM: user_interactions_learning_resource_id_interaction_type_watch_duration_index
            // TO:   idx_resource_type_duration (much shorter!)
            $table->index(
                ['learning_resource_id', 'interaction_type', 'watch_duration'], 
                'idx_resource_type_duration'
            );
            
            // More composite indexes with short names
            $table->index(['learning_resource_id', 'rating'], 'idx_resource_rating');
            $table->index(['interaction_type', 'engagement_score'], 'idx_type_engagement');
            $table->index('created_at', 'idx_created');
            
            // For analytics queries
            $table->index(['user_id', 'created_at'], 'idx_user_created');
            $table->index(['learning_resource_id', 'created_at'], 'idx_resource_created');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('user_interactions');
        Schema::enableForeignKeyConstraints();
    }
};