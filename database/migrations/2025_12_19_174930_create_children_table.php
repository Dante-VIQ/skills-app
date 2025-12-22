<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
 public function up(): void
    {
        Schema::create('children', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->constrained('users')->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('username')->unique(); // Username should be unique for login
            $table->string('email')->nullable(); // Email is optional and not unique
            $table->date('birth_date');
            $table->string('password'); // For child login
            $table->boolean('onboarding_completed')->default(false);
            $table->boolean('is_active')->default(true);
            $table->text('avatar')->nullable();
            $table->json('settings')->nullable(); // For child preferences
            $table->timestamps();
            
            // Indexes
            $table->index('parent_id');
            $table->index('username');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('children');
    }
};
