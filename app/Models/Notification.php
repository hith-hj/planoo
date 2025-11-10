<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

final class Notification extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_viewed' => 'bool',
            'payload' => 'json',
        ];
    }

    public function holder(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'belongTo_type', 'belongTo_id');
    }

    public function isBelongTo($holder)
    {
        if (
            $this->belongTo_id === $holder->id &&
            $this->belongTo_type === $holder::class
        ) {
            return true;
        }

        return false;
    }
}
