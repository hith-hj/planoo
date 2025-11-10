<?php

declare(strict_types=1);

use App\Models\Notification;

beforeEach(function () {
    $this->seed();
    $this->user('customer')->api();
    $this->url = '/api/customer/v1/notification';
    Notification::truncate();
});

describe('Notification Controller Tests', function () {
    it('returns all notifications for the authenticated customer', function () {
        Notification::factory(2)->for($this->user, 'holder')->create();
        $res = $this->getJson("{$this->url}/all")->assertOk();
        expect($res->json('payload'))->toHaveKeys(['notifications']);
        expect($res->json('payload.notifications'))->toHaveCount(2);
    });

    it('finds a specific notification by ID', function () {
        $notification = Notification::factory()->for($this->user, 'holder')->create();

        $res = $this->getJson("{$this->url}/find?notification_id={$notification->id}")
            ->assertOk();

        expect($res->json('payload.notification.id'))->toBe($notification->id);
    });

    it('fails to find an notification with invalid ID', function () {
        $this->getJson(
            "{$this->url}/find?notification_id=422"
        )->assertStatus(422);
    });

    it('set notification as viewed', function () {
        $notification = Notification::factory()->for($this->user, 'holder')->create();
        expect($notification->is_viewed)->toBeFalse();
        $this->postJson(
            "{$this->url}/view",
            ['notifications' => [$notification->id]]
        )->assertOk();
        expect($notification->fresh()->is_viewed)->toBeTrue();
    });

    it('delete notification', function () {
        $notification = Notification::factory()->for($this->user, 'holder')->create();
        $this->deleteJson(
            "{$this->url}/delete",
            ['notification_id' => $notification->id]
        )->assertOk();
        expect($notification->fresh())->toBeNull();
    });

    it('clear all notification', function () {
        Notification::factory(5)->for($this->user, 'holder')->create();
        expect($this->user->notifications()->count())->toBe(5);
        $this->postJson("{$this->url}/clear")->assertOk();
        expect($this->user->notifications()->count())->toBe(0);
    });
});
