<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\MediaHandler;
use App\Traits\ReviewHandler;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

final class Course extends Model
{
    use HasFactory;
    use MediaHandler;
    use ReviewHandler;

    protected $attributes = [
        'is_active' => false,
        'is_full' => false,
        'rate' => 0,
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'bool',
            'is_full' => 'bool',
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

    public function location(): HasOne
    {
        return $this->hasOne(Location::class, 'belongTo_id')
            ->withAttributes(['belongTo_type' => $this::class]);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'appointable_id')
            ->withAttributes(['appointable_type' => $this::class]);
    }

    public function customers()
    {
        return $this->belongsToMany(Customer::class)
            ->withPivot(['remaining_sessions', 'is_complete']);
    }
}
