<?php

declare(strict_types=1);

use App\Models\Tag;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('icon');
        });

        Schema::create('taggables', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Tag::class);
            $table->morphs('taggable');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tags');
        Schema::dropIfExists('taggables');
    }
};
