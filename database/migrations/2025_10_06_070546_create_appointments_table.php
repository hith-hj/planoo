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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->morphs('appointable');
            $table->foreignId('customer_id')->nullable();
            $table->date('date');
            $table->time('time');
            $table->integer('price');
            $table->integer('session_duration');
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
