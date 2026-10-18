<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\AccountStatus;
use App\Models\User;
use App\Traits\AccountAuthentication;

final class UserAuthServices
{
    use AccountAuthentication;

    protected const GUARD = 'partner:api';

    protected const MODEL = User::class;

    public function create(array $data): User
    {
        /** @var User $user */
        $user = User::create([
            'name' => $data['name'],
            'account_type' => $data['account_type'],
            'email' => $data['email'],
            'country_code' => $data['country_code'],
            'phone' => formatPhone($data['phone']),
            'firebase_token' => $data['firebase_token'],
            'description' => $data['description'],
            'password' => bcrypt($data['password']),
            'status' => AccountStatus::fresh->value,
            'is_notifiable' => true,
        ]);

        $user->fresh()->verify(by: $data['by'] ?? 'fcm');

        return $user;
    }
}
