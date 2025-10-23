<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

final class Tag extends Model
{
    /** @use HasFactory<\Database\Factories\TagFactory> */
    use HasFactory;

    public $timestamps = false;

    public function activities(): MorphToMany
    {
        return $this->morphedByMany(Activity::class, 'taggable');
    }

    public function courses(): MorphToMany
    {
        return $this->morphedByMany(Course::class, 'taggable');
    }
}
