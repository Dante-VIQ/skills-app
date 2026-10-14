<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('impact_contributions', function (Blueprint $table) {
            $table->foreignId('user_id')->after('id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->nullable()->after('user_id')->constrained('impact_projects')->nullOnDelete();
            $table->foreignId('child_id')->nullable()->after('project_id')->constrained('users')->nullOnDelete();
            $table->foreignId('activity_id')->nullable()->after('child_id')->constrained('child_activities')->nullOnDelete();

            $table->string('contribution_type')->default('learning'); // learning, donation, volunteering
            $table->decimal('contribution_value', 10, 2)->default(0);
            $table->string('currency', 3)->default('USD');

            $table->string('status')->default('pending'); // pending, confirmed, completed
            $table->text('notes')->nullable();

            $table->index('user_id');
            $table->index('project_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('impact_contributions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['project_id']);
            $table->dropForeign(['child_id']);
            $table->dropForeign(['activity_id']);
            $table->dropColumn([
                'user_id', 'project_id', 'child_id', 'activity_id',
                'contribution_type', 'contribution_value', 'currency',
                'status', 'notes',
            ]);
        });
    }
};