<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('codes', function (Blueprint $table): void {
            $table->json('booking_context')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('codes', function (Blueprint $table): void {
            $table->dropColumn('booking_context');
        });
    }
};
