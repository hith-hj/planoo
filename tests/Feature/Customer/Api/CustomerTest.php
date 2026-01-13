<?php

declare(strict_types=1);

use App\Models\Customer;
use App\Models\Media;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->seed();
    $this->user('customer')->api();
    $this->url = '/api/customer/v1/customer';
});

describe('Customer Controller Tests', function () {
    it('returns authenticated customer information', function () {
        $response = $this->getJson("{$this->url}/get")->assertOk();
        expect($response->json('payload.customer'))->not->toBeNull();
    });

    it('returns 401 for unauthorized customer', function () {
        $response = $this->clearUser()->getJson("{$this->url}/get");
        $response->assertStatus(401);
    });

    it('updates customer information', function () {
        $customerData = Customer::factory()->make()->toArray();
        $response = $this->postJson("{$this->url}/update", $customerData)
            ->assertOk();

        expect($response->json('payload.customer.name'))->toBe($customerData['name']);
    });

    it('uploads profile image', function () {
        Storage::fake('public');
        $media = Media::factory()->fakeFile('kosa.jpeg');

        $res = $this->postJson("{$this->url}/uploadProfileImage", [
            'profile_image' => $media,
        ])->assertOk();

        $fileName = $this->getFileName($res->json('payload.profile_image.url'));
        Storage::disk('public')->assertExists("uploads/images/customers/{$this->user->id}/{$fileName}");
    });

    it('deletes profile image', function () {
        Storage::fake('public');
        $media = Media::factory()->fakeFile('kosa.jpeg');

        $res = $this->postJson("{$this->url}/uploadProfileImage", [
            'profile_image' => $media,
        ])->assertOk();

        expect($this->user->fresh()->medias()->count())->toBe(1);

        $fileName = $this->getFileName($res->json('payload.profile_image.url'));
        Storage::disk('public')->assertExists("uploads/images/customers/{$this->user->id}/{$fileName}");

        $this->postJson("{$this->url}/deleteProfileImage")->assertOk();

        expect($this->user->fresh()->medias()->count())->toBe(0);
        Storage::disk('public')->assertMissing("uploads/images/customers/{$this->user->id}/{$fileName}");
    });
});
