<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\MediaHandler;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Court extends Model
{
    use HasFactory;
    use MediaHandler;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function hasSiblings(): bool
    {
        return $this->activities()->exists()
        || $this->courses()->exists()
        || $this->events()->exists();
    }
}
