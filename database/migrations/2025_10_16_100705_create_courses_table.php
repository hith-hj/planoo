<?php

declare(strict_types=1);

use App\Models\Category;
use App\Models\Course;
use App\Models\Customer;
use App\Models\User;
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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->foreignIdFor(Category::class);
            $table->string('name');
            $table->text('description');
            $table->boolean('is_active');
            $table->boolean('is_full');
            $table->integer('price');
            $table->integer('session_duration');
            $table->integer('capacity');
            $table->integer('rate')->default(0);
            $table->integer('cancellation_fee')->nullable();
            $table->timestamps();
        });

        Schema::create('course_customer', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Course::class);
            $table->foreignIdFor(Customer::class);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
        Schema::dropIfExists('course_customer');
    }
};
