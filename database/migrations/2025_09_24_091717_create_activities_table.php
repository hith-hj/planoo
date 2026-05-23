<?php

declare(strict_types=1);

use App\Models\Category;
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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->foreignIdFor(Category::class);
            $table->string('name');
            $table->integer('price');
            $table->integer('session_duration');
            $table->text('description');
            $table->boolean('is_active');
            $table->integer('rate')->default(0);
            $table->timestamps();
            $table->index(['is_active', 'category_id', 'rate']);
            $table->index(['is_active', 'category_id', 'price']);
            $table->index('session_duration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
