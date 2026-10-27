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

        $response = $this->getJson(route('partner.activity.find', ['activity_id' => $activity->id]))
            ->assertOk();

        expect($response->json('payload.activity.id'))->toBe($activity->id);
    });

    it('fails to find an activity with invalid ID', function () {
        $this->getJson(route('partner.activity.find', ['activity_id' => 422]))->assertStatus(422);
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
        $activity = Activity::factory()
            ->for($this->user, 'user')
            ->for($this->user->courts()->first(), 'court')
            ->create();
        $activity->update(['name' => 'tido']);

        $updatePayload = ['activity_id' => $activity->id, ...$activity->toArray()];

        $response = $this->patchJson(route('partner.activity.update'), $updatePayload);
        $response->assertOk();

        expect($response->json('payload.activity.name'))->toBe('tido');
    });

    it('fails to update an activity with invalid data', function () {
        $this->patchJson(route('partner.activity.update'), [])->assertStatus(422);
    });

    it('updates the full day schedule and preserves ids for retained weekdays', function () {
        $activity = Activity::factory()->for($this->user, 'user')
            ->for($this->user->courts()->first(), 'court')->create();
        $activity->appointments()->delete();
        $activity->days()->delete();
        $monday = $activity->days()->create(['day' => 'monday', 'start' => '09:00', 'end' => '12:00']);
        $removed = $activity->days()->create(['day' => 'tuesday', 'start' => '09:00', 'end' => '12:00']);
        $payload = ['activity_id' => $activity->id, ...$activity->toArray(), 'replace_days' => true, 'days' => [
            ['day' => 'monday', 'start' => '10:00', 'end' => '14:00'],
            ['day' => 'friday', 'start' => '08:30', 'end' => '11:30', 'is_active' => false],
        ]];

        $response = $this->patchJson(route('partner.activity.update'), $payload)->assertOk();
        expect($response->json('payload.activity.days'))->toHaveCount(2);
        expect($activity->days()->where('day', 'monday')->first()->id)->toBe($monday->id);
        expect($monday->fresh()->start)->toStartWith('10:00');
        expect(Day::find($removed->id))->toBeNull();
        expect($activity->days()->where('day', 'friday')->first()->is_active)->toBeFalse();

        // Resending the same schedule must succeed without recreating its days.
        $ids = $activity->days()->pluck('id')->all();
        $this->patchJson(route('partner.activity.update'), $payload)->assertOk();
        expect($activity->days()->pluck('id')->all())->toBe($ids);
    });

    it('keeps days unchanged when omitted from an activity update', function () {
        $activity = Activity::factory()->for($this->user, 'user')
            ->for($this->user->courts()->first(), 'court')->create();
        $days = $activity->days()->get()->toArray();
        $this->patchJson(route('partner.activity.update'), [
            'activity_id' => $activity->id, ...$activity->toArray(), 'name' => 'Updated activity',
        ])->assertOk();
        expect($activity->days()->get()->toArray())->toBe($days);
    });

    it('rejects invalid schedules without changing the activity', function (array $days) {
        $activity = Activity::factory()->for($this->user, 'user')
            ->for($this->user->courts()->first(), 'court')->create();
        $originalDays = $activity->days()->get()->toArray();
        $this->patchJson(route('partner.activity.update'), [
            'activity_id' => $activity->id, ...$activity->toArray(), 'name' => 'Must not save', 'days' => $days,
        ])->assertUnprocessable();
        expect($activity->fresh()->name)->toBe($activity->name)
            ->and($activity->days()->get()->toArray())->toBe($originalDays);
    })->with([
        'empty schedule' => [[]],
        'invalid weekday' => [[['day' => 'invalid', 'start' => '09:00', 'end' => '12:00']]],
        'equal times' => [[['day' => 'monday', 'start' => '12:00', 'end' => '12:00']]],
        'invalid time' => [[['day' => 'monday', 'start' => '25:15', 'end' => '12:00']]],
        'duplicate weekday' => [[
            ['day' => 'monday', 'start' => '09:00', 'end' => '12:00'],
            ['day' => 'monday', 'start' => '13:00', 'end' => '17:00'],
        ]],
    ]);

    it('protects upcoming bookings while allowing compatible schedule changes', function () {
        $activity = Activity::factory()->for($this->user, 'user')
            ->for($this->user->courts()->first(), 'court')->create();
        $activity->days()->delete();
        $day = $activity->days()->create(['day' => 'monday', 'start' => '09:00', 'end' => '12:00']);
        $activity->appointments()->update([
            'date' => now()->next('monday')->toDateString(), 'time' => '10:00', 'end_at' => '11:00', 'session_duration' => 60,
        ]);
        $payload = ['activity_id' => $activity->id, ...$activity->toArray(), 'name' => 'Changed', 'days' => [
            ['day' => 'monday', 'start' => '11:00', 'end' => '14:00'],
        ]];

        $this->patchJson(route('partner.activity.update'), $payload)->assertStatus(400)
            ->assertJson(['message' => 'The schedule cannot exclude an upcoming accepted appointment.']);
        expect($activity->fresh()->name)->toBe($activity->name)
            ->and($day->fresh()->start)->toStartWith('09:00');

        $payload['days'][0]['start'] = '08:00';
        $this->patchJson(route('partner.activity.update'), $payload)->assertOk();
        expect($day->fresh()->start)->toStartWith('08:00');
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
