<?php

declare(strict_types=1);

use App\Models\Notification;

beforeEach(function () {
    $this->seed();
    $this->user('partner', 'stadium')->api();
    Notification::truncate();
});

describe('Notification Controller Tests', function () {
    it('returns all notifications for the authenticated partner', function () {
        Notification::factory(2)->for($this->user, 'holder')->create();
        $res = $this->getJson(route('partner.notification.all'))->assertOk();
        expect($res->json('payload'))->toHaveKeys(['notifications']);
        expect($res->json('payload.notifications'))->toHaveCount(2);
    });

    it('return true when there are new notifications', function () {
        Notification::factory(2)->for($this->user, 'holder')->create(['is_viewed' => 0]);
        $res = $this->getJson(route('partner.notification.checkNew'))->assertOk();
        expect($res->json('payload'))->toHaveKeys(['new'])
            ->and($res->json('payload.new'))->toBe(true);
    });

    it('return false when there are no new notifications', function () {
        Notification::factory(2)->for($this->user, 'holder')->create(['is_viewed' => 1]);
        $res = $this->getJson(route('partner.notification.checkNew'))->assertOk();
        expect($res->json('payload'))->toHaveKeys(['new'])
            ->and($res->json('payload.new'))->toBe(false);
    });

    it('finds a specific notification by ID', function () {
        $notification = Notification::factory()->for($this->user, 'holder')->create();

        $res = $this->getJson(route('partner.notification.find', ['notification_id' => $notification->id]))
            ->assertOk();

        expect($res->json('payload.notification.id'))->toBe($notification->id);
    });

    it('fails to find an notification with invalid ID', function () {
        $this->getJson(
            route('partner.notification.find', ['notification_id' => 1231])
        )->assertStatus(422);
    });

    it('set notification as viewed', function () {
        $notification = Notification::factory()->for($this->user, 'holder')->create();
        expect($notification->is_viewed)->toBeFalse();
        $this->postJson(
            route('partner.notification.view'),
            ['notifications' => [$notification->id]]
        )->assertOk();
        expect($notification->fresh()->is_viewed)->toBeTrue();
    });

    it('delete notification', function () {
        $notification = Notification::factory()->for($this->user, 'holder')->create();
        $this->deleteJson(
            route('partner.notification.delete'),
            ['notification_id' => $notification->id]
        )->assertOk();
        expect($notification->fresh())->toBeNull();
    });

    it('clear all notification', function () {
        Notification::factory(5)->for($this->user, 'holder')->create();
        expect($this->user->notifications()->count())->toBe(5);
        $this->postJson(route('partner.notification.clear'))->assertOk();
        expect($this->user->notifications()->count())->toBe(0);
    });
});
