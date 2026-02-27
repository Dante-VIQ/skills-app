<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add parent relationship column
            if (!Schema::hasColumn('users', 'parent_id')) {
                $table->foreignId('parent_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            // Add parent code for invitations
            if (!Schema::hasColumn('users', 'parent_code')) {
                $table->string('parent_code', 8)
                    ->nullable()
                    ->unique()
                    ->after('remember_token');
            }

            // Add other missing columns
            if (!Schema::hasColumn('users', 'first_name')) {
                $table->string('first_name')->after('id');
                $table->string('last_name')->after('first_name');
            }

            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username')->unique()->after('email');
            }

            if (!Schema::hasColumn('users', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable()->after('username');
            }

            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('date_of_birth');
            }

            if (!Schema::hasColumn('users', 'interests')) {
                $table->json('interests')->nullable()->after('avatar');
            }

            if (!Schema::hasColumn('users', 'skills')) {
                $table->json('skills')->nullable()->after('interests');
            }

            if (!Schema::hasColumn('users', 'onboarding_completed')) {
                $table->boolean('onboarding_completed')->default(false)->after('skills');
            }

            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('onboarding_completed');
            }

            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('updated_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn([
                'parent_id',
                'parent_code',
                'first_name',
                'last_name',
                'username',
                'date_of_birth',
                'avatar',
                'interests',
                'skills',
                'onboarding_completed',
                'is_active',
                'last_login_at'
            ]);
        });
    }
};
