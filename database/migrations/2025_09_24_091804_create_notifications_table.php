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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->ulidMorphs('belongTo');
            $table->boolean('is_viewed');
            $table->smallInteger('type');
            $table->string('title');
            $table->string('body');
            $table->text('payload');
            $table->timestamps();

            $table->index(['belongTo_type', 'belongTo_id'], 'notifications_belongto_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
