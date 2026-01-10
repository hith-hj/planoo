<?php

declare(strict_types=1);

use App\Models\Activity;
use App\Models\Course;
use App\Models\Event;

beforeEach(function () {
	$this->seed();
	$this->user('customer')->api();
	$this->url = '/api/customer/v1/home';
});

describe('Activity Controller Tests', function () {
	it('returns feeds for customer', function () {
		Activity::factory()->create();
		Course::factory()->create();
		Event::factory()->create();
		$res = $this->getJson("{$this->url}/feeds")->assertOk();
		expect($res->json('payload'))->toHaveKeys(['feeds'])
			->and($res->json('payload.feeds'))->toHaveKeys(['activities', 'courses', 'events']);
	});

	it('returns recommended for customer', function () {
		Activity::factory()->create();
		Course::factory()->create();
		Event::factory()->create();
		$res = $this->getJson("{$this->url}/recommended")->assertOk();
		expect($res->json('payload'))->toHaveKeys(['recommended'])
			->and($res->json('payload.recommended'))->toHaveKeys(['activities', 'courses', 'events']);
	});

	it('returns featured for customer', function () {
		Activity::factory()->create();
		Course::factory()->create();
		Event::factory()->create();
		$res = $this->getJson("{$this->url}/featured")->assertOk();
		expect($res->json('payload'))->toHaveKeys(['featured'])
			->and($res->json('payload.featured'))->toHaveKeys(['activity', 'course', 'event'])
			->and($res->json('payload.featured.activity'))->toHaveCount(1)
			->and($res->json('payload.featured.course'))->toHaveCount(1)
			->and($res->json('payload.featured.event'))->toHaveCount(1);
	});

	it('returns search for customer', function () {
		$activity = Activity::factory()->create();
		$res = $this->getJson("{$this->url}/search?owner=activity&search=$activity->name");
		$res->assertOk();
		expect($res->json('payload'))->toHaveKeys(['result'])
			->and($res->json('payload.result'))->toBeIterable();
	});

	it('fails to returns search for customer with invalid owner', function () {
		$res = $this->getJson("{$this->url}/search?owner=invalid&search=something");
		$res->assertStatus(400);
	});
});
