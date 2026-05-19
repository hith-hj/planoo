<?php

declare(strict_types=1);

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
        Schema::table('activities', function (Blueprint $table) {
            $table->index(['is_active', 'category_id', 'rate']);
            $table->index(['is_active', 'category_id', 'price']);
            $table->index('session_duration');
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->index(['is_active', 'is_full', 'category_id', 'rate']);
            $table->index(['is_active', 'is_full', 'category_id', 'price']);
            $table->index(['is_active', 'is_full', 'session_duration']);
            $table->index(['is_active', 'is_full', 'course_duration']);
        });

        Schema::table('events', function (Blueprint $table) {
            $table->index(['is_active', 'is_full', 'category_id', 'rate']);
            $table->index(['is_active', 'is_full', 'category_id', 'admission_fee']);
            $table->index(['is_active', 'is_full', 'start_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropIndex(['is_active', 'category_id', 'rate']);
            $table->dropIndex(['is_active', 'category_id', 'price']);
            $table->dropIndex(['session_duration']);
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->dropIndex(['is_active', 'is_full', 'category_id', 'rate']);
            $table->dropIndex(['is_active', 'is_full', 'category_id', 'price']);
            $table->dropIndex(['is_active', 'is_full', 'session_duration']);
            $table->dropIndex(['is_active', 'is_full', 'course_duration']);
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropIndex(['is_active', 'is_full', 'category_id', 'rate']);
            $table->dropIndex(['is_active', 'is_full', 'category_id', 'admission_fee']);
            $table->dropIndex(['is_active', 'is_full', 'start_date']);
        });
    }
};
