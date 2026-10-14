<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tutor_education', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tutor_id')->constrained('users')->onDelete('cascade');

            $table->string('degree');
            $table->string('field_of_study');
            $table->string('institution');
            $table->year('graduation_year')->nullable();
            $table->string('grade')->nullable();
            $table->text('description')->nullable();

            $table->timestamps();

            $table->index('tutor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tutor_education');
    }
};