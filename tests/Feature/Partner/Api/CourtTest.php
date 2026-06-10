<?php

use App\Models\User;
use App\Models\Court;
use App\Models\Activity;
use App\Models\Course;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
    $this->user('partner', 'stadium')->api();
});

test('partner can list all their courts', function () {
    Court::factory()->count(3)->for($this->user, 'user')->create();
    Court::factory()->create(['user_id' => User::factory()->create()->id]);

    $response = $this->getJson(route('partner.court.all'));
    $response->assertStatus(200);
    expect($response->json('payload.courts'))->ToHaveCount($this->user->courts()->count());
});

test('partner can find a specific court belonging to them', function () {
    $court = Court::factory()->for($this->user, 'user')->create();

    $response = $this->getJson(route('partner.court.find',['court_id' => $court->id]));
    $response->assertStatus(200)->assertJsonPath('payload.court.id', $court->id);
});

test('partner can create a court with valid data', function () {
    $courtData = [
        'name' => 'Alley Oop Arena',
    ];

    $response = $this->postJson(route('partner.court.create'), $courtData);
    $response->assertStatus(200);
    $this->assertDatabaseHas('courts', [
        'user_id' => $this->user->id,
        'name' => 'Alley Oop Arena',
    ]);
});

test('partner can update an existing court', function () {
    $court = Court::factory()->for($this->user, 'user')->create(['name' => 'Old Name']);

    $response = $this->postJson(route('partner.court.update'), [
            'court_id' => $court->id,
            'name' => 'New Name',
        ]);

    $response->assertStatus(200);
    $this->assertDatabaseHas('courts', [
        'id' => $court->id,
        'name' => 'New Name',
    ]);
});

test('partner can delete a court if they have multiple courts and no siblings', function () {
    $courtToDelete = Court::factory()->for($this->user, 'user')->create();
    Court::factory()->for($this->user, 'user')->create();

    $response = $this->deleteJson(route('partner.court.delete'), ['court_id' => $courtToDelete->id]);

    $response->assertStatus(200);
    $this->assertDatabaseMissing('courts', ['id' => $courtToDelete->id]);
});

test('partner cannot delete their last remaining court', function () {
    $this->user->courts()->delete();
    $onlyCourt = Court::factory()->for($this->user, 'user')->create();

    $response = $this->deleteJson(route('partner.court.delete'), ['court_id' => $onlyCourt->id]);
    $response->assertStatus(400);
    $this->assertDatabaseHas('courts', ['id' => $onlyCourt->id]);
});

test('partner cannot delete a court that has linked siblings', function ($siblingRelation, $siblingFactory) {
    $courtToDelete = Court::factory()->for($this->user, 'user')->create();
    Court::factory()->for($this->user, 'user')->create();;

    $siblingFactory::factory()->create(['court_id' => $courtToDelete->id]);

    $response = $this->deleteJson(route('partner.court.delete'), ['court_id' => $courtToDelete->id]);
    $response->assertStatus(400);
    $this->assertDatabaseHas('courts', ['id' => $courtToDelete->id]);
})->with([
    'has activities' => ['activities', Activity::class],
    'has courses'    => ['courses', Course::class],
    'has events'     => ['events', Event::class],
]);
