<?php

declare(strict_types=1);

use App\Models\Activity;
use App\Models\Media;
use Illuminate\Support\Facades\Storage;


beforeEach(function () {
	$this->seed();
	$this->user('partner', 'stadium')->api();
	$this->url = '/api/partner/v1/media';
	Storage::fake('public');
});

describe('Media Controller Tests', function () {
    it('returns media for a valid activity', function () {
        $activity = Activity::factory()->for($this->user, 'user')->create();

        $res = $this->getJson("{$this->url}/all/activity/{$activity->id}")
            ->assertOk();

        expect($res->json('payload.medias'))->not->toBeNull()
            ->and(count($res->json('payload.medias')))->toBe($activity->medias->count());
    });

    it('returns 404 for media of an invalid activity', function () {
        $this->getJson("{$this->url}/get/activity/1000")->assertStatus(404);
    });

    it('uploads multiple images for an activity', function () {
        $activity = Activity::factory()->for($this->user, 'user')->create();
        $res = $this->postJson("{$this->url}/create/activity/{$activity->id}", [
            'type' => 'image',
            'media' => Media::factory()->medias(2),
        ])->assertOk();

        foreach ($res->json('payload.medias') as $file) {
	        $fileName = $this->getFileName($file['url']);

            Storage::disk('public')->assertExists("uploads/images/activities/{$activity->id}/{$fileName}");
        }
    });

    it('cant uploads more than 5 medias', function () {
        $activity = Activity::factory()->for($this->user, 'user')->create();
        $this->postJson("{$this->url}/create/activity/{$activity->id}", [
            'type' => 'image',
            'media' => Media::factory()->medias(6),
        ])->assertStatus(422);
    });

    it('cant uploads more media if limit is reached', function () {
        $activity = Activity::factory()->for($this->user, 'user')->create();

        $this->postJson("{$this->url}/create/activity/{$activity->id}", [
            'type' => 'image',
            'media' => Media::factory()->medias(5),
        ])->assertOk();

        $this->postJson("{$this->url}/create/activity/{$activity->id}", [
            'type' => 'image',
            'media' => Media::factory()->medias(1),
        ])->assertStatus(400);
    });

    it('deletes media for an activity', function () {
        $activity = Activity::factory()->for($this->user, 'user')->create();
		$activity->medias()->delete();

		$res = $this->postJson("{$this->url}/create/activity/{$activity->id}", [
            'type' => 'image',
            'media' => Media::factory()->medias(1),
        ])->assertOk();
        expect($activity->fresh()->medias()->count())->toBe(1);
        foreach($res->json('payload.medias') as $media){
	        $fileName = $this->getFileName($media['url']);

	        Storage::disk('public')->assertExists("uploads/images/activities/{$activity->id}/{$fileName}");

	        $this->deleteJson("{$this->url}/delete/activity/{$activity->id}", [
	            'media_id' => $media['id'],
	        ])->assertOk();

	        expect($activity->fresh()->medias()->count())->toBe(0);
	        Storage::disk('public')->assertMissing("uploads/images/activities/{$activity->id}/{$fileName}");
    	}
    });
});
