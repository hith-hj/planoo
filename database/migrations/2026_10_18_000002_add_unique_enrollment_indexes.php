<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['course_customer' => 'course_id', 'customer_event' => 'event_id'] as $table => $owner) {
            if (DB::table($table)->select($owner, 'customer_id')->groupBy($owner, 'customer_id')->havingRaw('COUNT(*) > 1')->exists()) {
                throw new RuntimeException("Resolve duplicate enrollments in {$table} before adding the unique index.");
            }
        }
        Schema::table('course_customer', function (Blueprint $table): void {
            $table->unique(['course_id', 'customer_id']);
        });
        Schema::table('customer_event', function (Blueprint $table): void {
            $table->unique(['event_id', 'customer_id']);
        });
    }

    public function down(): void
    {
        Schema::table('course_customer', function (Blueprint $table): void {
            $table->dropUnique(['course_id', 'customer_id']);
        });
        Schema::table('customer_event', function (Blueprint $table): void {
            $table->dropUnique(['event_id', 'customer_id']);
        });
    }
};
