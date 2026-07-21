<?php

use App\Models\Court;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
	$this->seed();
	$this->user('customer')->api();
	Court::truncate();
});

describe('Court controller tests', function () {
	test('customer can list courts', function () {
		Court::factory()->count(3)->create();

		$response = $this->getJson(route('customer.court.all'));
		$response->assertStatus(200);
		expect($response->json('payload.courts'))->ToHaveCount(3);
	});

	test('customer can paginate courts', function () {
		Court::factory()->count(3)->create();

		$response = $this->getJson(route('customer.court.all',['page'=>2,'perPage'=>1]));
		$response->assertStatus(200);
		expect($response->json('payload.courts'))->ToHaveCount(1);
	});

	test('customer can find a specific court by id', function () {
		$court = Court::factory()->create();

		$response = $this->getJson(route('customer.court.find', ['court_id' => $court->id]));
		$response->assertStatus(200);
		expect($response->json('payload.court.id'))->toBe($court->id)
			->and($response->json('payload.court'))->toHaveKeys([
				'activities',
				'courses',
				'events',
			])
			->and($response->json('payload.court.activities'))->not->toBeNull()
			->and($response->json('payload.court.courses'))->not->toBeNull()
			->and($response->json('payload.court.events'))->not->toBeNull();
	});

	test('customer can search for a court', function () {
		$court = Court::factory()->create();

		$response = $this->getJson(route('customer.court.search', ['name' => $court->name]));
		$response->assertStatus(200);
		expect($response->json('payload.court.0.id'))->toBe($court->id)
			->and($response->json('payload.court.0'))->toHaveKeys([
				'activities',
				'courses',
				'events',
			]);
	});

	test('customer can not find a court with invalid name', function () {

		$response = $this->getJson(route('customer.court.search', ['name' => 'invalid_name']));
		$response->assertStatus(404);
	});

});
