<?php

declare(strict_types=1);

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignIdFor(User::class);
            $table->foreignIdFor(Category::class);
            $table->foreignUlid('court_id')->nullable();
            $table->string('name');
            $table->text('description');
            $table->boolean('is_active');
            $table->boolean('is_full');
            $table->integer('event_duration');
            $table->integer('capacity');
            $table->integer('rate')->default(0);
            $table->integer('admission_fee')->nullable();
            $table->integer('withdrawal_fee')->nullable();
            $table->tinyInteger('status');
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->timestamps();

            // indexs
            $table->index(['is_active', 'is_full', 'category_id', 'rate']);
            $table->index(['is_active', 'is_full', 'category_id', 'admission_fee']);
            $table->index(['is_active', 'is_full', 'start_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
