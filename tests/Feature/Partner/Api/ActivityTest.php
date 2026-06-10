<?php

declare(strict_types=1);

use App\Models\Activity;
use App\Models\Court;
use App\Models\Day;
use App\Models\Location;
use App\Models\Media;
use App\Models\Tag;

beforeEach(function () {
    $this->seed();
    $this->user('partner', 'stadium')->api();
});

describe('Activity Controller Tests', function () {
    it('returns all activities for the authenticated partner', function () {
        $this->user->activities()->delete();
        Activity::factory(2)->for($this->user, 'user')->create();
        $response = $this->getJson(route('partner.activity.all'))->assertOk();

        expect($response->json('payload.activities'))->toHaveCount(2);
    });

    it('finds a specific activity by ID', function () {
        $activity = Activity::factory()->for($this->user, 'user')->create();

        $response = $this->getJson(route('partner.activity.find',['activity_id'=>$activity->id]))
            ->assertOk();

        expect($response->json('payload.activity.id'))->toBe($activity->id);
    });

    it('fails to find an activity with invalid ID', function () {
        $this->getJson(route('partner.activity.find',['activity_id'=>422]))->assertStatus(422);
    });

    it('creates a new activity with days, location, media, and tags', function () {
        $activityData = Activity::factory()
            ->for($this->user, 'user')
            ->for($this->user->courts()->first(), 'court')
            ->make()->toArray();
        $days = Day::factory()->days();
        $location = Location::factory()->make()->toArray();
        $media = [
            'type' => 'image',
            'media' => Media::factory()->medias(2),
        ];
        $tags = Tag::factory()->tags()->make()->toArray();

        $payload = array_merge($activityData, ['days' => $days], $location, $media, $tags);

        $response = $this->postJson(route('partner.activity.create'), $payload)->assertOk();
        expect($response->json('payload.activity'))->not->toBeNull();

        $createdId = $response->json('payload.activity.id');
        $createdActivity = Activity::with(['tags', 'days', 'medias', 'location'])->find($createdId);

        expect($createdActivity->days()->count())->toBe(count($days))
            ->and($createdActivity->court)->not->toBeNull()
            ->and($createdActivity->category)->not->toBeNull()
            ->and($createdActivity->location)->not->toBeNull()
            ->and($createdActivity->location->long)->toBe($location['long'])
            ->and($createdActivity->tags)->not->toBeNull()
            ->and($createdActivity->medias)->not->toBeNull()
            ->and($createdActivity->medias->count())->toBe(2);
    });

    it('creates a new activity without tags', function () {
        $activityData = Activity::factory()
            ->for($this->user, 'user')
            ->for($this->user->courts()->first(), 'court')
            ->make()->toArray();

        $days = Day::factory()->days();
        $location = Location::factory()->make()->toArray();
        $media = [
            'type' => 'image',
            'media' => Media::factory()->medias(2),
        ];

        $payload = array_merge($activityData, ['days' => $days], $location, $media);
        $response = $this->postJson(route('partner.activity.create'), $payload)->assertOk();

        expect($response->json('payload.activity'))->not->toBeNull();

        $createdId = $response->json('payload.activity.id');
        $createdActivity = Activity::with(['days', 'medias', 'location'])->find($createdId);

        expect($createdActivity->days()->count())->toBe(count($days))
            ->and($createdActivity->location)->not->toBeNull()
            ->and($createdActivity->location->long)->toBe($location['long'])
            ->and($createdActivity->tags)->not->toBeNull()
            ->and($createdActivity->medias)->not->toBeNull()
            ->and($createdActivity->medias->count())->toBe(2);
    });

    it('fails to creates a new activity with invalid court id ', function () {
        $activityData = Activity::factory()
            ->for($this->user, 'user')
            ->make(['court_id' => Court::factory()->create()->id])->toArray();
        $response = $this->postJson(route('partner.activity.create'), $activityData);
        $response->assertStatus(400);
    });

    it('fails to create an activity with invalid data', function () {
        $this->postJson(route('partner.activity.create'), [])->assertStatus(422);
    });

    it('updates an existing activity', function () {
        $activity = Activity::factory()->for($this->user, 'user')->create();
        $activity->update(['name' => 'tido']);

        $updatePayload = ['activity_id' => $activity->id, ...$activity->toArray()];

        $response = $this->patchJson(route('partner.activity.update'), $updatePayload)->assertOk();

        expect($response->json('payload.activity.name'))->toBe('tido');
    });

    it('fails to update an activity with invalid data', function () {
        $this->patchJson(route('partner.activity.update'), [])->assertStatus(422);
    });

    it('deletes an activity', function () {
        $activity = Activity::factory()->for($this->user, 'user')->create();

        $this->deleteJson(route('partner.activity.delete'), [
            'activity_id' => $activity->id,
        ])->assertOk();

        expect(Activity::find($activity->id))->toBeNull();
    });

    it('toggles the activation status of an activity', function () {
        $activity = Activity::factory()
            ->for($this->user, 'user')
            ->create(['is_active' => false]);

        $this->postJson(route('partner.activity.toggleActivation'), [
            'activity_id' => $activity->id,
        ])->assertOk();

        expect($activity->fresh()->is_active)->toBeTrue();
    });
});
