<?php

declare(strict_types=1);

use App\Models\Category;
use App\Models\Court;
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
            $table->ulid('id');
            $table->foreignIdFor(User::class);
            $table->foreignIdFor(Category::class);
            $table->foreignIdFor(Court::class)->nullable();
            $table->string('name');
            $table->text('description');
            $table->boolean('is_active');
            $table->boolean('is_full');
            $table->integer('price');
            $table->integer('course_duration');
            $table->integer('capacity');
            $table->integer('rate')->default(0);
            $table->integer('cancellation_fee')->nullable();
            $table->timestamp('start_date')->nullable();
            $table->tinyInteger('status');
            $table->timestamps();

            // indexs
            $table->index(['is_active', 'is_full', 'category_id', 'rate']);
            $table->index(['is_active', 'is_full', 'category_id', 'price']);
            $table->index(['is_active', 'is_full', 'course_duration']);
            $table->index(['is_active', 'is_full', 'start_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
