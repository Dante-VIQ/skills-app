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
        Schema::create('revenue_streams', function (Blueprint $table) {
            $table->id();
            $table->string('source');
            $table->string('campaign_name')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('NGN');
            $table->boolean('is_recurring')->default(false);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['source', 'start_date']);
            $table->index('is_recurring');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('revenue_streams');
    }
};
