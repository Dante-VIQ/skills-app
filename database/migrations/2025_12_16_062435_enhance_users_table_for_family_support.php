<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('type', ['child', 'parent'])->default('child');
            $table->date('birth_date')->nullable();
            $table->string('avatar')->nullable();
            $table->string('username')->unique()->nullable(); // For child login
            $table->boolean('onboarding_completed')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['type', 'birth_date', 'avatar', 'username', 'onboarding_completed']);
        });
    }
};
