<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;


final class Code extends Model
{
    use HasFactory;

    protected function casts():array
    {
        return [
            'code' => 'int',
            'expire_at' => 'datetime',
        ];
    }

    public function isValid(): bool
    {
        return $this->expire_at > now();
    }

    public function holder(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'belongTo_type', 'belongTo_id');
    }
}
