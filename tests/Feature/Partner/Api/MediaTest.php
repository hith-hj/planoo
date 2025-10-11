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

        $response = $this->getJson("{$this->url}/all/activity/{$activity->id}")
            ->assertOk();

        expect($response->json('payload.medias'))->not->toBeNull()
            ->and(count($response->json('payload.medias')))->toBe($activity->medias->count());
    });

    it('returns 404 for media of an invalid activity', function () {
        $this->getJson("{$this->url}/get/activity/1000")->assertStatus(404);
    });

    it('uploads multiple images for an activity', function () {
        $activity = Activity::factory()->for($this->user, 'user')->create();
        $mediaFiles = [Media::factory()->fakeMedia(), Media::factory()->fakeMedia()];

        $res = $this->postJson("{$this->url}/create/activity/{$activity->id}", [
            'type' => 'image',
            'media' => $mediaFiles,
        ])->assertOk();

        foreach ($res->json('payload.media') as $file) {
	        $fileName = $this->getFileName($file['url']);

            Storage::disk('public')->assertExists("uploads/images/activities/{$activity->id}/{$fileName}");
        }
    });

    it('deletes media for an activity', function () {
        $activity = Activity::factory()->for($this->user, 'user')->create();
		$activity->medias()->delete();

		$res = $this->postJson("{$this->url}/create/activity/{$activity->id}", [
            'type' => 'image',
            'media' => [Media::factory()->fakeMedia()],
        ])->assertOk();
        expect($activity->fresh()->medias()->count())->toBe(1);
        foreach($res->json('payload.media') as $media){
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
