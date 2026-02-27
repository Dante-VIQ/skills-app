<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('parent_assignments')) {
            Schema::create('parent_assignments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('parent_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('child_id')->constrained('users')->onDelete('cascade');
                $table->string('title');
                $table->text('description');
                $table->timestamp('due_date');
                $table->string('status')->default('pending'); // pending, submitted_for_review, completed, overdue
                $table->timestamp('completed_at')->nullable();
                $table->text('feedback')->nullable();
                $table->integer('rating')->nullable();
                $table->string('priority')->default('medium'); // low, medium, high
                $table->timestamps();

                $table->index(['parent_id', 'status']);
                $table->index(['child_id', 'status', 'due_date']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('parent_assignments');
    }
};
