<?php

declare(strict_types=1);

use App\Models\Activity;
use App\Models\Tag;

beforeEach(function () {
    $this->seed();
    $this->user('partner', 'stadium')->api();
});

describe('Tag Controller Test', function () {
    it('returns tags for a valid activity', function () {
        $activity = Activity::factory()->for($this->user, 'user')->create();

        $response = $this->getJson(route('partner.tag.all', ['owner_type' => 'activity', 'owner_id' => $activity->id]))
            ->assertOk();

        expect($response->json('payload.tags'))->not->toBeNull();
    });

    it('returns 404 for tags of an invalid activity', function () {
        $this->getJson(route('partner.tag.all', ['owner_type' => 'activity', 'owner_id' => 123]))->assertStatus(404);
    });

    it('assigns new tags to an activity', function () {
        $activity = Activity::factory()->for($this->user, 'user')->create();
        $activity->tags()->detach();

        $tags = Tag::inRandomOrder()->take(2)->pluck('id')->toArray();

        $this->postJson(route('partner.tag.create', ['owner_type' => 'activity', 'owner_id' => $activity->id]), [
            'tags' => $tags,
        ])->assertOk();

        expect($activity->fresh()->tags->pluck('id')->toArray())->toMatchArray($tags);
    });

    it('adds new tags without duplicating existing ones', function () {
        $activity = Activity::factory()->for($this->user, 'user')->create();
        $activity->tags()->detach();
        $activity->tags()->attach(1);

        expect($activity->fresh()->tags()->count())->toBe(1);

        $this->postJson(route('partner.tag.create', ['owner_type' => 'activity', 'owner_id' => $activity->id]), [
            'tags' => [1, 2],
        ])->assertOk();

        expect($activity->fresh()->tags()->count())->toBe(2);
    });

    it('deletes tags from an activity', function () {
        $activity = Activity::factory()->for($this->user, 'user')->create();
        $activity->tags()->detach();
        $activity->tags()->attach(3);

        expect($activity->fresh()->tags()->count())->toBe(1);

        $this->deleteJson(route('partner.tag.delete', ['owner_type' => 'activity', 'owner_id' => $activity->id]), [
            'tags' => [3],
        ])->assertOk();

        expect($activity->fresh()->tags()->count())->toBe(0);
    });
});
