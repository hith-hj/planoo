<?php

declare(strict_types=1);

use App\Models\Customer;
use App\Services\CustomerServices;

beforeEach(function () {
    $this->customerServices = new CustomerServices();
    $this->customerData = [
        'phone' => '0911111111',
        'name' => 'dodge',
    ];
});

describe('Customer Services', function () {
    it('get customer when exists', function () {
        $customer = Customer::factory()->create($this->customerData);
        expect($customer->name)->toBe($this->customerData['name']);
        $res = $this->customerServices->createIfNotExists($this->customerData);
        expect($res)->toBeInstanceOf(Customer::class);
        expect($res->phone)->toBe($this->customerData['phone']);
        expect($res->name)->toBe($customer->name);
    });

    it('create customer when not exists', function () {
        expect(Customer::all())->toHaveCount(0);
        $res = $this->customerServices->createIfNotExists($this->customerData);
        expect($res)->toBeInstanceOf(Customer::class);
        expect($res->phone)->toBe($this->customerData['phone']);
        expect(Customer::all())->toHaveCount(1);
    });

    it('fails to create customer with wrong data', function () {
        $this->customerServices->createIfNotExists([]);
    })->throws(Exception::class);

    it('find customer by id', function () {
        $customer = Customer::factory()->create();
        $res = $this->customerServices->find($customer->id);
        expect($res)->toBeInstanceOf(Customer::class);
        expect($res->id)->toBe($customer->id);
        expect($res->name)->toBe($customer->name);
    });

    it('fails to find customer by id if not exists', function () {
        $this->customerServices->find(9999);
    })->throws(Exception::class);

});
