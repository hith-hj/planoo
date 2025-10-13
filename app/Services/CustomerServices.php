<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\CustomerStatus;
use App\Models\Customer;

final class CustomerServices
{
    public function createIfNotExists(array $data): Customer
    {
        $data = checkAndCastData($data, ['phone' => 'string']);
        $customer = Customer::where('phone', $data['phone'])->first();
        if ($customer) {
            return $customer;
        }
        $data['name'] = $this->userName($data);
        $data['password'] = $data['phone'];

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
        ]);

        $customer = Customer::create([
            'name' => $this->userName($data),
            'phone' => $data['phone'],
            'password' => bcrypt($data['password']),
            'status' => CustomerStatus::fresh->value,
        ]);

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

        return 'usr_'.mt_rand(10000, 90000);
    }
}
