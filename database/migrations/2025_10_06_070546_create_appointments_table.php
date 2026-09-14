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
        Schema::create('appointments', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulidMorphs('appointable');
            $table->foreignUlid('customer_id')->nullable();
            $table->date('date');
            $table->time('time');
            $table->time('end_at');
            $table->integer('session_duration');
            $table->integer('price');
            $table->tinyInteger('status');
            $table->string('notes')->nullable();
            $table->string('canceled_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
