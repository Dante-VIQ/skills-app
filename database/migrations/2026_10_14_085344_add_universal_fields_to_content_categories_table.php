<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('content_categories', function (Blueprint $table) {
            $table->boolean('is_universal')->default(false)->after('description');
            $table->boolean('parent_enforced')->default(false)->after('is_universal');
            $table->integer('required_watch_minutes')->nullable()->after('parent_enforced');
        });
    }

    public function down(): void
    {
        Schema::table('content_categories', function (Blueprint $table) {
            $table->dropColumn([
                'is_universal',
                'parent_enforced',
                'required_watch_minutes',
            ]);
        });
    }
};