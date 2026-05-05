<?php

declare(strict_types=1);

use App\Models\Activity;
use App\Models\Course;
use App\Models\Event;
use App\Models\Media;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->seed();
    $this->user('partner', 'stadium')->api();
    $this->url = '/api/partner/v1/user';
});

describe('User Controller Tests', function () {
    it('returns authenticated user information', function () {
        $response = $this->getJson("{$this->url}/get")->assertOk();
        expect($response->json('payload.user'))->not->toBeNull();
    });

    it('returns 401 for unauthorized user', function () {
        $response = $this->clearUser()->getJson("{$this->url}/get");
        $response->assertStatus(401);
    });

    it('updates user information', function () {
        $userData = User::factory()->make()->toArray();
        $response = $this->postJson("{$this->url}/update", $userData)
            ->assertOk();

        expect($response->json('payload.user.name'))->toBe($userData['name']);
    });

    it('uploads profile image', function () {
        Storage::fake('public');
        $media = Media::factory()->fakeFile('kosa.jpeg');

        $res = $this->postJson("{$this->url}/uploadProfileImage", [
            'profile_image' => $media,
        ])->assertOk();

        $fileName = $this->getFileName($res->json('payload.profile_image.url'));
        Storage::disk('public')->assertExists("uploads/images/users/{$this->user->id}/{$fileName}");
    });

    it('deletes profile image', function () {
        Storage::fake('public');
        $media = Media::factory()->fakeFile('kosa.jpeg');

        $res = $this->postJson("{$this->url}/uploadProfileImage", [
            'profile_image' => $media,
        ])->assertOk();

        expect($this->user->fresh()->medias()->count())->toBe(1);

        $fileName = $this->getFileName($res->json('payload.profile_image.url'));
        Storage::disk('public')->assertExists("uploads/images/users/{$this->user->id}/{$fileName}");

        $this->postJson("{$this->url}/deleteProfileImage")->assertOk();

        expect($this->user->fresh()->medias()->count())->toBe(0);
        Storage::disk('public')->assertMissing("uploads/images/users/{$this->user->id}/{$fileName}");
    });

    it('delete user account', function () {
        User::truncate();
        Activity::truncate();
        Event::truncate();
        Course::truncate();
        $user = User::factory()->create();
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('activities', 1);
        $this->assertDatabaseCount('courses', 1);
        $this->assertDatabaseCount('events', 1);
        $this->replaceUser($user);
        $res = $this->deleteJson(route('partner.user.delete'));
        $res->assertOk();
        expect($user->fresh())->toBeNull();
        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('activities', 0);
        $this->assertDatabaseCount('courses', 0);
        $this->assertDatabaseCount('events', 0);
    });
});
