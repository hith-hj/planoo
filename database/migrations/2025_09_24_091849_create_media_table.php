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
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            // $table->ulidMorphs('belongTo');
            $table->string('belongTo_type', 100);
            $table->string('belongTo_id', 26);
            $table->string('url');
            $table->string('type')->nullable();
            $table->string('name', 50)->nullable();
            $table->timestamps();

            $table->index(['belongTo_type', 'belongTo_id', 'name'], 'media_belongto_name_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
