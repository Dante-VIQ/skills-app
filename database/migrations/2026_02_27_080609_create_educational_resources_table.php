<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Store trusted educational sources
        Schema::create('educational_sources', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('website_url');
            $table->text('description')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('favicon_url')->nullable();

            $table->boolean('is_trusted')->default(true);
            $table->integer('trust_score')->default(80);
            $table->json('safety_rating')->nullable();

            $table->boolean('requires_attribution')->default(true);
            $table->string('attribution_format')->nullable();
            $table->string('referral_link_format')->nullable();

            $table->json('available_topics')->nullable();
            $table->json('content_types')->nullable();

            $table->boolean('has_api')->default(false);
            $table->string('api_base_url')->nullable();
            $table->string('api_key')->nullable();

            $table->integer('referral_count')->default(0);
            $table->integer('click_through_rate')->default(0);

            $table->timestamps();
            $table->index('slug');
            $table->index('trust_score');
        });

        // Store curated external resources
        Schema::create('curated_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_id')->constrained('educational_sources')->onDelete('cascade');

            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('summary')->nullable();
            $table->string('resource_url');
            $table->string('canonical_url')->nullable();
            $table->string('external_id')->nullable();

            $table->string('topic');
            $table->string('subtopic')->nullable();
            $table->json('tags')->nullable();
            $table->string('difficulty_level');
            $table->string('content_type');
            $table->integer('estimated_minutes')->nullable();

            $table->string('thumbnail_url')->nullable();

            $table->string('author_name')->nullable();
            $table->string('author_url')->nullable();
            $table->string('license')->nullable();

            $table->json('feature_vector')->nullable();
            $table->json('keyword_vector')->nullable();
            $table->float('popularity_score')->default(0);
            $table->integer('referral_count')->default(0);

            $table->boolean('is_safe')->default(true);
            $table->json('safety_check_results')->nullable();
            $table->timestamp('safety_checked_at')->nullable();

            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('quality_score')->default(70);

            $table->timestamps();
            $table->timestamp('last_referred_at')->nullable();

            // Indexes
            $table->index('source_id');
            $table->index('topic');
            $table->index('difficulty_level');
            $table->index('content_type');
            $table->index('popularity_score');
            $table->index('quality_score');
            $table->index('last_referred_at');

            $table->index(['topic', 'difficulty_level']);
            $table->index(['source_id', 'content_type']);
            $table->index(['is_active', 'quality_score']);

            // ✅ Driver-aware fulltext index
            if (in_array(DB::connection()->getDriverName(), ['mysql', 'pgsql'])) {
                $table->fullText(['title', 'description', 'summary']);
            }
        });

        // Track referrals
        Schema::create('resource_referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('curated_resource_id')->constrained()->onDelete('cascade');
            $table->foreignId('learning_resource_id')->nullable()->constrained()->nullOnDelete();

            $table->string('referral_type');
            $table->float('ml_score')->nullable();
            $table->json('context')->nullable();

            $table->boolean('clicked')->default(false);
            $table->timestamp('clicked_at')->nullable();
            $table->integer('time_spent')->nullable();
            $table->boolean('found_helpful')->nullable();

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