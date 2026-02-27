<?php
// database/migrations/2026_02_27_000001_create_external_referrals_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Store trusted educational sources
        Schema::create('educational_sources', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // "W3Schools", "Khan Academy", etc.
            $table->string('slug')->unique();
            $table->string('website_url');
            $table->text('description')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('favicon_url')->nullable();
            
            // Trust and safety
            $table->boolean('is_trusted')->default(true);
            $table->integer('trust_score')->default(80); // 0-100
            $table->json('safety_rating')->nullable(); // Content safety assessment
            
            // Referral settings
            $table->boolean('requires_attribution')->default(true);
            $table->string('attribution_format')->nullable(); // "Via {source_name}"
            $table->string('referral_link_format')->nullable(); // URL structure
            
            // Content categories available
            $table->json('available_topics')->nullable();
            $table->json('content_types')->nullable(); // ['tutorial', 'reference', 'exercise']
            
            // API integration (optional)
            $table->boolean('has_api')->default(false);
            $table->string('api_base_url')->nullable();
            $table->string('api_key')->nullable();
            
            // Usage tracking
            $table->integer('referral_count')->default(0);
            $table->integer('click_through_rate')->default(0);
            
            $table->timestamps();
            $table->index('slug');
            $table->index('trust_score');
        });

        // Store curated external resources (metadata only, no content)
        Schema::create('curated_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_id')->constrained('educational_sources')->onDelete('cascade');
            
            // Basic metadata (what we store)
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('summary')->nullable(); // Our original summary
            $table->string('resource_url'); // The actual link
            $table->string('canonical_url')->nullable(); // Original URL
            
            // External identifier (if from API)
            $table->string('external_id')->nullable();
            
            // Categorization for ML
            $table->string('topic');
            $table->string('subtopic')->nullable();
            $table->json('tags')->nullable();
            $table->string('difficulty_level'); // beginner, intermediate, advanced
            $table->string('content_type'); // tutorial, reference, exercise, video, article
            $table->integer('estimated_minutes')->nullable();
            
            // Preview image (thumbnail from source)
            $table->string('thumbnail_url')->nullable();
            
            // Attribution
            $table->string('author_name')->nullable();
            $table->string('author_url')->nullable();
            $table->string('license')->nullable();
            
            // ML features
            $table->json('feature_vector')->nullable(); // For ML similarity
            $table->json('keyword_vector')->nullable(); // Extracted keywords
            $table->float('popularity_score')->default(0);
            $table->integer('referral_count')->default(0); // How many times we referred
            
            // Safety
            $table->boolean('is_safe')->default(true);
            $table->json('safety_check_results')->nullable();
            $table->timestamp('safety_checked_at')->nullable();
            
            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('quality_score')->default(70); // Our rating
            
            $table->timestamps();
            $table->timestamp('last_referred_at')->nullable();
            
            // Indexes for ML queries
            $table->index('source_id');
            $table->index('topic');
            $table->index('difficulty_level');
            $table->index('content_type');
            $table->index('popularity_score');
            $table->index('quality_score');
            $table->index('last_referred_at');
            
            // Composite indexes for ML filtering
            $table->index(['topic', 'difficulty_level']);
            $table->index(['source_id', 'content_type']);
            $table->index(['is_active', 'quality_score']);
            
            // Full text index for search
            $table->fullText(['title', 'description', 'summary']);
        });

        // Track referrals (click-throughs)
        Schema::create('resource_referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('curated_resource_id')->constrained()->onDelete('cascade');
            $table->foreignId('learning_resource_id')->nullable()->constrained()->nullOnDelete();
            
            // Context of referral
            $table->string('referral_type'); // 'ml_recommendation', 'search', 'related', 'curated'
            $table->float('ml_score')->nullable(); // ML score at time of referral
            $table->json('context')->nullable(); // What user was doing
            
            // Outcome tracking
            $table->boolean('clicked')->default(false);
            $table->timestamp('clicked_at')->nullable();
            $table->integer('time_spent')->nullable(); // seconds on external site (if we can track via return)
            $table->boolean('found_helpful')->nullable(); // User feedback
            
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index(['curated_resource_id', 'clicked']);
            $table->index('referral_type');
        });
    }

    public function down()
    {
        Schema::dropIfExists('resource_referrals');
        Schema::dropIfExists('curated_resources');
        Schema::dropIfExists('educational_sources');
    }
};