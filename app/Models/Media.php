<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Media extends Model
{
    /** @use HasFactory<\Database\Factories\MediaFactory> */
    use HasFactory;

    // protected $attributes = ['type' => 'image'];

    public function holder()
    {
        return $this->morphTo(__METHOD__, 'belongTo_type', 'belongTo_id');
    }
}
