<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

final class Day extends Model
{
    /** @use HasFactory<\Database\Factories\DayFactory> */
    use HasFactory;

    protected $attributes = [
        'is_active' => true,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'belongTo_type',
        'belongTo_id',
        'day',
        'start',
        'end',
        'is_active',
    ];

    protected static function booted()
    {
        self::updated(function ($day) {
            if ($day->holder) {
                $day->holder->touch();
            }
        });
    }

    protected function casts()
    {
        return [
            'is_active' => 'bool',
        ];
    }

    protected function fullSessionLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->day." [ {$this->start} - {$this->end} ]",
        );
    }

    public function holder(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'belongTo_type', 'belongTo_id');
    }
}
