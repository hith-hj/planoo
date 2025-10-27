<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\AccountStatus;
use App\Models\Customer;

final class CustomerServices
{
    public function get(int|string|null $id): Customer
    {
        Required($id, 'customer id');
        $customer = Customer::find($id);
        NotFound($customer, 'customer');

        return $customer->load(['medias']);
    }

    public function createIfNotExists(array $data): Customer
    {
        $data = checkAndCastData($data, ['phone' => 'string']);
        $customer = Customer::where('phone', $data['phone'])->first();
        if ($customer) {
            return $customer;
        }
        $data['name'] = $this->userName($data);
        $data['password'] = $data['phone'];
        $data['firebase_token'] = 'not-set';

        return $this->create($data);
    }

    public function find(int $id): Customer
    {
        Required($id, 'customer id');
        $customer = Customer::find($id);
        NotFound($customer, 'Not Found');

        return $customer;
    }

    public function create(array $data): Customer
    {
        $data = checkAndCastData($data, [
            'phone' => 'string',
            'name' => 'string',
            'password' => 'string',
            'firebase_token' => 'string',
        ]);

        $customer = Customer::create([
            'name' => $this->userName($data),
            'phone' => $data['phone'],
            'password' => bcrypt($data['password']),
            'status' => AccountStatus::fresh->value,
            'firebase_token' => $data['firebase_token'],
            'is_active' => true,
            'is_notifiable' => true,
        ]);

        return $customer;
    }

    public function getCustomer(array $data)
    {
        $customer = null;
        if (isset($data['customer_id']) || isset($data['customer_phone'])) {
            if (isset($data['customer_phone'])) {
                $customer = $this->createIfNotExists([
                    'phone' => $data['customer_phone'],
                ]);
            }
            if (isset($data['customer_id'])) {
                $customer = $this->find((int) $data['customer_id']);
            }
        }
        NotFound($customer, 'customer');
        return $customer;
    }

    private function userName(array $data): string
    {
        if (isset($data['name'])) {
            return $data['name'];
        }

        if (isset($data['phone'])) {
            return 'usr_'.mb_substr($data['phone'], -5);
        }

        return 'usr_'.random_int(10000, 90000);
    }
}
