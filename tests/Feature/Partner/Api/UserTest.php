<?php

declare(strict_types=1);

use App\Models\Activity;
use App\Models\Media;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
	$this->seed();
	$this->user('partner', 'stadium')->api();
	$this->url = '/api/partner/v1/user';
});

describe('user Controller tests', function () {
	it('returns user information', function () {
		$res = $this->getJson("$this->url/get")->assertOk();
		expect($res->json('payload.user'))->not->toBeNull();
	});

	it('fails to return user info for unauthorized user', function () {
		$res = $this->clearUser()->getJson("$this->url/get");
		$res->assertStatus(401);
	});

	it('can update user information', function () {
		$user = User::factory()->make()->toArray();
		$res = $this->postJson("$this->url/update",$user);
		expect($res->json('payload.user.name'))->toBe($user['name']);
	});

	it('upload profile image',function(){
		Storage::fake('public');
		$media = Media::factory()->fakeFile('kosa.jpeg');
		$res = $this->postJson("$this->url/uploadProfileImage",[
			'profile_image' => $media
		]);
		$fileName = time() . '_' . $media->hashName();
		Storage::disk('public')
				->assertExists("uploads/images/users/{$this->user->id}/$fileName");
	});

	it('delete profile image',function(){
		Storage::fake('public');
		$media = Media::factory()->fakeFile('kosa.jpeg');
		$this->postJson("$this->url/uploadProfileImage",[
			'profile_image' => $media
		]);
		$fileName = time() . '_' . $media->hashName();
		Storage::disk('public')
				->assertExists("uploads/images/users/{$this->user->id}/$fileName");
		$res = $this->postJson("$this->url/deleteProfileImage");
		Storage::disk('public')
				->assertMissing("uploads/images/users/{$this->user->id}/$fileName");
	});

});
