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

describe('media Controller tests', function () {
	it('returns media for activity', function () {
		$activity = Activity::factory()->for($this->user, 'user')->create();
		$res = $this->getJson("$this->url/all/activity/{$activity->id}");
		$res->assertOk();
		expect($res->json('payload.medias'))->not->toBeNull()
			->and(count($res->json('payload.medias')))->toBe($activity->medias->count());
	});

	it('fails returns tag for invalid activity', function () {
		$this->getJson("$this->url/get/activity/1000")->assertStatus(404);
	});

	test('can be uploaded images', function () {
		$activity = Activity::factory()->for($this->user, 'user')->create();
		$media = [Media::factory()->fakeMedia(), Media::factory()->fakeMedia()];
		$this->postJson("$this->url/create/activity/$activity->id", [
			'type' => 'image',
			'media' => $media,
		])->assertOk();

		foreach ($media as $item) {
			$fileName = time() . '_' . $item['file']->hashName();
			Storage::disk('public')
				->assertExists("uploads/images/activities/$activity->id/$fileName");
		}
	});

});
