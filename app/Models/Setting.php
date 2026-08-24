<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Setting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'value',
        'description',
    ];

    protected static function booted()
    {
        // keep in sync with the cache keys used by the app_setting() helper
        self::saved(fn ($setting) => cache()->forget('app_setting.'.$setting->name));
        self::deleted(fn ($setting) => cache()->forget('app_setting.'.$setting->name));
    }
}
