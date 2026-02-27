<?php
// database/migrations/2026_02_27_000003_create_learning_resource_external_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('learning_resource_external', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_resource_id')->constrained()->onDelete('cascade');
            $table->foreignId('external_resource_id')->constrained('curated_resources')->onDelete('cascade');
            $table->string('relationship_type')->default('supplemental'); // primary, supplemental, prerequisite
            $table->integer('display_order')->default(0);
            $table->timestamps();
            
            $table->index('learning_resource_id');
            $table->index('external_resource_id');
            $table->index('relationship_type');
            
            $table->unique(['learning_resource_id', 'external_resource_id'], 'lr_external_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('learning_resource_external');
    }
};