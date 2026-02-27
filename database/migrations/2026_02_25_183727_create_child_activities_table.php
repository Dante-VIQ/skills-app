<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('child_activities')) {
            Schema::create('child_activities', function (Blueprint $table) {
                $table->id();
                $table->foreignId('child_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('assignment_id')->nullable()->constrained('parent_assignments')->nullOnDelete();
                $table->foreignId('content_module_id')->nullable()->constrained('content_modules')->nullOnDelete();
                $table->string('type');
                $table->string('title');
                $table->text('description')->nullable();
                $table->json('data')->nullable();
                $table->integer('duration')->nullable();
                $table->integer('score')->nullable();
                $table->integer('max_score')->nullable();
                $table->string('status')->default('completed');
                $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('reviewed_at')->nullable();
                $table->timestamps();

                $table->index(['child_id', 'created_at']);
                $table->index(['type', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('child_activities');
    }
};
