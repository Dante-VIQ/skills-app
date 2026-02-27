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
        Schema::create('user_interests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // These should match your controller
            $table->string('category'); // Make sure this exists
            $table->string('subcategory')->nullable(); // Make sure this exists

            // OR if you used different names:
            // $table->string('interest_category');
            // $table->string('interest_subcategory')->nullable();

            $table->integer('interest_level')->default(3);
            $table->string('source')->default('explicit');
            $table->timestamp('last_updated_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_interests');
    }
};
