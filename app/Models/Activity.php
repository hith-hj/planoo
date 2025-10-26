<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\MediaHandler;
use App\Traits\ReviewHandler;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

final class Activity extends Model
{
    use HasFactory;
    use MediaHandler;
    use ReviewHandler;

    protected $attributes = [
        'is_active' => false,
        'rate' => 0,
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'bool',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function days(): MorphMany
    {
        return $this->morphMany(Day::class, 'belongTo');
    }

    public function location(): MorphOne
    {
        return $this->morphOne(Location::class, 'belongTo');
    }

    public function appointments(): MorphMany
    {
        return $this->morphMany(Appointment::class, 'appointable');
    }
}
