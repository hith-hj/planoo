<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Location extends Model
{
    /** @use HasFactory<\Database\Factories\LocationFactory> */
    use HasFactory;

    public function holder()
    {
        return $this->morphTo(__FUNCTION__, 'belongTo_type', 'belongTo_id');
    }
}
