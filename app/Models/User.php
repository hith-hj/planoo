<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\CodeHandler;
use App\Traits\MediaHandler;
use App\Traits\NotificationsHandler;
use App\Traits\VerificationHandler;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

final class User extends Authenticatable implements JWTSubject
{
    use CodeHandler;
    use HasFactory;
    use MediaHandler;
    use NotificationsHandler;
    use VerificationHandler;

    protected $attributes = ['rate' => 0];

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
            'is_notifiable' => 'bool',
        ];
    }

    // protected $hidden = [
    //     'password'
    // ];

    public function getJWTIdentifier(): int|string
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}
