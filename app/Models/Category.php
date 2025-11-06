<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;

    public $timestamps = false;

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}
