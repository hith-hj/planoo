<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;

    public $timestamps = false;

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}
