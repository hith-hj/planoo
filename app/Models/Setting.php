<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Setting extends Model
{
    use HasFactory;

    protected static function booted()
    {
        self::saved(fn ($setting) => cache()->forget('setting.'.$setting->name));
        self::deleted(fn ($setting) => cache()->forget('setting.'.$setting->name));
    }
}
