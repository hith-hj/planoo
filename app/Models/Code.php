<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Code extends Model
{
    use HasFactory;

    protected function casts()
    {
        return [
            'code' => 'int',
            'expire_at' => 'datetime',
        ];
    }
}
