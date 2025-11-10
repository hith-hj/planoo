<?php

declare(strict_types=1);

namespace App\Models;

use App\Interfaces\Locatable;
use App\Interfaces\Notifiable;
use App\Traits\CodeHandler;
use App\Traits\NotificationsHandler;
use App\Traits\VerificationHandler;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

final class Customer extends Authenticatable implements JWTSubject, Locatable, Notifiable
{
    use CodeHandler;
    use HasFactory;
    use NotificationsHandler;
    use VerificationHandler;

    protected $attributes = [
        'is_active' => true,
    ];

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
            'is_notifiable' => 'bool',
        ];
    }

    public function getJWTIdentifier(): int|string
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }

    public function location(): MorphOne
    {
        return $this->morphOne(Location::class, 'belongTo');
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class)
            ->withPivot(['remaining_sessions', 'is_complete'])
            ->withTimestamps();
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class)
            ->withTimestamps();
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
