<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ========== CREATE TABLES IN CORRECT ORDER ==========

        // 1. Core tables (already exist from users migration)
        
        // 2. Content Categories (must come first)
        if (!Schema::hasTable('content_categories')) {
            Schema::create('content_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->boolean('is_universal')->default(false);
                $table->boolean('parent_enforced')->default(false);
                $table->integer('required_watch_minutes')->default(0);
                $table->string('icon')->nullable();
                $table->string('color')->nullable();
                $table->timestamps();
            });
        }

        // 3. Content Modules (depends on content_categories)
        if (!Schema::hasTable('content_modules')) {
            Schema::create('content_modules', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description')->nullable();
                $table->enum('type', ['video', 'article', 'interactive', 'quiz', 'project']);
                $table->string('url');
                $table->string('thumbnail')->nullable();
                $table->integer('duration_minutes')->default(5);
                $table->integer('difficulty_level')->default(1);
                $table->integer('age_min')->default(0);
                $table->integer('age_max')->default(18);
                $table->foreignId('category_id')->nullable()->constrained('content_categories')->onDelete('set null');
                $table->string('source')->default('youtube');
                $table->string('external_id')->nullable();
                $table->json('metadata')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 4. Universal Skill Requirements (depends on content_categories)
        if (!Schema::hasTable('universal_skill_requirements')) {
            Schema::create('universal_skill_requirements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('parent_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('child_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('category_id')->constrained('content_categories')->onDelete('cascade');
                $table->integer('required_minutes_per_week');
                $table->boolean('is_active')->default(true);
                $table->boolean('blocks_content')->default(true);
                $table->timestamps();
                
                $table->unique(['child_id', 'category_id']);
            });
        }

        // 5. Social Impact Projects (independent)
        if (!Schema::hasTable('social_impact_projects')) {
            Schema::create('social_impact_projects', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->enum('type', ['school', 'lab', 'library', 'community_center', 'scholarship', 'other']);
                $table->text('description');
                $table->string('location');
                $table->decimal('target_amount', 12, 2);
                $table->decimal('amount_raised', 12, 2)->default(0);
                $table->integer('children_impacted')->default(0);
                $table->json('progress_photos')->nullable();
                $table->json('updates')->nullable();
                $table->timestamp('start_date')->nullable();
                $table->timestamp('completion_date')->nullable();
                $table->boolean('is_active')->default(true);
                $table->boolean('is_featured')->default(false);
                $table->string('status')->default('planning');
                $table->timestamps();
            });
        }

        // 6. Watch Time Records (depends on content_modules and content_categories)
        if (!Schema::hasTable('watch_time_records')) {
            Schema::create('watch_time_records', function (Blueprint $table) {
                $table->id();
                $table->foreignId('child_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('content_id')->constrained('content_modules')->onDelete('cascade');
                $table->foreignId('category_id')->nullable()->constrained('content_categories')->onDelete('set null');
                $table->integer('minutes_watched');
                $table->date('watch_date');
                $table->decimal('engagement_score', 3, 2)->default(1.0);
                $table->timestamps();
                
                $table->unique(['child_id', 'content_id', 'watch_date']);
            });
        }

        // 7. Child Impact Contributions (depends on social_impact_projects)
        if (!Schema::hasTable('child_impact_contributions')) {
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
            });
        }

        // 8. Parent Assignments
        if (!Schema::hasTable('parent_assignments')) {
            Schema::create('parent_assignments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('parent_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('child_id')->constrained('users')->onDelete('cascade');
                $table->string('title');
                $table->text('description')->nullable();
                $table->enum('type', ['quiz', 'activity', 'challenge', 'reflection']);
                $table->json('content');
                $table->timestamp('assigned_at')->useCurrent();
                $table->timestamp('due_date')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->enum('status', ['assigned', 'in_progress', 'completed', 'overdue'])->default('assigned');
                $table->boolean('blocks_content')->default(true);
                $table->timestamps();
            });
        }

        // 9. Child Activities
        if (!Schema::hasTable('child_activities')) {
            Schema::create('child_activities', function (Blueprint $table) {
                $table->id();
                $table->foreignId('child_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('assignment_id')->nullable()->constrained('parent_assignments')->onDelete('cascade');
                $table->string('activity_type');
                $table->json('activity_data');
                $table->json('child_responses');
                $table->integer('score')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
            });
        }

        // 10. Revenue Streams
        if (!Schema::hasTable('revenue_streams')) {
            Schema::create('revenue_streams', function (Blueprint $table) {
                $table->id();
                $table->string('source');
                $table->string('campaign_name')->nullable();
                $table->decimal('amount', 10, 2);
                $table->string('currency')->default('NGN');
                $table->boolean('is_recurring')->default(false);
                $table->date('start_date');
                $table->date('end_date')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();
            });
        }

        // 11. Pivot tables (must come after main tables)
        if (Schema::hasTable('interests') && !Schema::hasTable('content_module_interests')) {
            Schema::create('content_module_interests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('content_module_id')->constrained('content_modules')->onDelete('cascade');
                $table->foreignId('interest_id')->constrained('interests')->onDelete('cascade');
                $table->integer('relevance_score')->default(1);
                $table->timestamps();
                
                $table->unique(['content_module_id', 'interest_id']);
            });
        }

        if (Schema::hasTable('skills') && !Schema::hasTable('content_module_skills')) {
            Schema::create('content_module_skills', function (Blueprint $table) {
                $table->id();
                $table->foreignId('content_module_id')->constrained('content_modules')->onDelete('cascade');
                $table->foreignId('skill_id')->constrained('skills')->onDelete('cascade');
                $table->integer('strength_score')->default(1);
                $table->timestamps();
                
                $table->unique(['content_module_id', 'skill_id']);
            });
        }
    }

    public function down(): void
    {
        // Drop in reverse order (respect foreign key constraints)
        Schema::dropIfExists('content_module_skills');
        Schema::dropIfExists('content_module_interests');
        Schema::dropIfExists('revenue_streams');
        Schema::dropIfExists('child_activities');
        Schema::dropIfExists('parent_assignments');
        Schema::dropIfExists('child_impact_contributions');
        Schema::dropIfExists('watch_time_records');
        Schema::dropIfExists('universal_skill_requirements');
        Schema::dropIfExists('social_impact_projects');
        Schema::dropIfExists('content_modules');
        Schema::dropIfExists('content_categories');
    }
};