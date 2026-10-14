<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tutor_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');

            // Professional info
            $table->string('headline')->nullable();
            $table->text('bio')->nullable();
            $table->string('profile_photo')->nullable();
            $table->string('video_intro')->nullable();

            // Expertise & teaching
            $table->json('expertise_areas')->nullable();
            $table->json('teaching_styles')->nullable();
            $table->json('languages')->nullable();
            $table->string('timezone')->default('UTC');

            // Experience & education
            $table->integer('years_experience')->default(0);
            $table->string('highest_education')->nullable();
            $table->string('institution')->nullable();

            // Verification
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();

            // Contact preferences
            $table->string('whatsapp_number')->nullable();
            $table->boolean('show_whatsapp')->default(false);
            $table->boolean('allow_direct_calls')->default(false);
            $table->boolean('allow_messages')->default(true);

            // Availability (JSON weekly schedule)
            $table->json('availability')->nullable();

            // Rates
            $table->decimal('hourly_rate', 8, 2)->nullable();
            $table->json('package_rates')->nullable();

            // Stats (auto-calculated)
            $table->float('rating')->default(0);
            $table->integer('total_reviews')->default(0);
            $table->integer('total_students')->default(0);
            $table->integer('total_hours')->default(0);
            $table->integer('profile_views')->default(0);

            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('accepting_students')->default(true);

            $table->timestamps();

            // Indexes
            $table->index('is_verified');
            $table->index('rating');
            $table->index('is_active');
            $table->index('is_featured');
            $table->index('accepting_students');
            $table->index('hourly_rate');
            $table->index('years_experience');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tutor_profiles');
    }
};