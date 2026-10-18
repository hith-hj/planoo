<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\AccountStatus;
use App\Models\Customer;
use App\Traits\AccountAuthentication;

final class CustomerAuthServices
{
    use AccountAuthentication;

    protected const GUARD = 'customer:api';

    protected const MODEL = Customer::class;

    public function create(array $data): Customer
    {
        /** @var Customer $customer */
        $customer = Customer::create([
            'name' => $data['name'],
            'country_code' => $data['country_code'],
            'phone' => formatPhone($data['phone']),
            'email' => $data['email'],
            'gender' => $data['gender'],
            'birthdate' => $data['birthdate'],
            'firebase_token' => $data['firebase_token'],
            'password' => bcrypt($data['password']),
            'status' => AccountStatus::fresh->value,
            'is_active' => (bool) app_setting('new_customer_activation_status', true),
            'is_notifiable' => true,
        ]);

        $customer->fresh()->verify(by: $data['by'] ?? 'fcm');

        return $customer;
    }
}
