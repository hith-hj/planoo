<?php

declare(strict_types=1);

use App\Models\Customer;
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
        Schema::create('course_customer', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('course_id');
            $table->foreignIdFor(Customer::class);
            $table->integer('remaining_sessions');
            $table->boolean('is_complete')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_customer');
    }
};
