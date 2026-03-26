<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('team_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('email');
            $table->string('role')->default('member');
            $table->string('token', 64)->unique();
            $table->text('message')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('declined_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            
            // Index for faster lookups
            $table->index(['team_id', 'email']);
            $table->index('token');
            $table->index('expires_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('team_invitations');
    }
};