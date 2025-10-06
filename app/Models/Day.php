<?php

declare(strict_types=1);

namespace App\Models;

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

    protected function casts()
    {
        return [
            'is_active' => 'bool',
        ];
    }

    public function holder(): MorphTo
    {
        return $this->morphTo(__METHOD__, 'belongTo_type', 'belongTo_id');
    }
}
