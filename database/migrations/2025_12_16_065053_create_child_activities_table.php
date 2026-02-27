<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('child_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('content_module_id')->nullable()->constrained('content_modules')->onDelete('set null');
            $table->foreignId('assignment_id')->nullable()->constrained('parent_assignments')->onDelete('set null');
            $table->string('type'); // module_started, module_completed, quiz_attempt, assignment_submission, etc.
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('data')->nullable(); // Activity-specific data
            $table->integer('duration')->nullable(); // Time spent in minutes
            $table->integer('score')->nullable();
            $table->integer('max_score')->nullable();
            $table->float('percentage_score')->nullable(); // Calculated from score/max_score
            $table->string('status')->default('in_progress'); // in_progress, completed, failed, reviewed
            $table->json('responses')->nullable(); // User's responses for quizzes
            $table->json('metadata')->nullable(); // Additional ML training data
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('feedback')->nullable();
            $table->timestamps();
            
            $table->index(['child_id', 'type', 'created_at']);
            $table->index(['child_id', 'status']);
            $table->index('content_module_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('child_activities');
    }
};