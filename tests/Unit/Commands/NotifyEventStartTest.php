<?php

use App\Enums\AppointmentStatus;
use App\Enums\EventStatus;
use App\Enums\NotificationTypes;
use App\Models\Appointment;
use App\Models\Event;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;

use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
	$this->seed();
	Carbon::setTestNow(Carbon::create(2025, 11, 4, 10));
	Appointment::truncate();
	Notification::truncate();
});

describe('Notify Event Start Test', function () {
	it('activates event and creates appointment when event starts today', function () {
		$user = User::factory()->create();
		$event = Event::factory()
			->hasDays(['day' => 'tuesday', 'start' => '10:00', 'end' => '12:00'])
			->hasCustomers(2)
			->create([
				'user_id' => $user->id,
				'start_date' => Carbon::now()->toDateString(),
				'status' => EventStatus::pending->value,
			]);
		$event->appointments()->delete();
		Artisan::call('app:neb');
		$event->refresh();
		expect($event->status)->toBe(EventStatus::active->value)
			->and($event->appointments)->toHaveCount(1);
	});

	it('notifies event is soon when event starts tomorrow', function () {
		$user = User::factory()->create();
		$event = Event::factory()
			->hasDays(['day' => 'wednesday', 'start' => '10:00', 'end' => '12:00'])
			->hasCustomers(1)
			->create([
				'user_id' => $user->id,
				'start_date' => Carbon::now()->addDay()->toDateString(),
				'status' => EventStatus::pending->value,
			]);
		Artisan::call('app:neb');
		assertDatabaseHas('notifications', [
			'type' => NotificationTypes::event->value,
		]);
	});

	it('skips events not starting today or tomorrow', function () {
		$event = Event::factory()
			->create([
				'start_date' => Carbon::now()->addDays(3)->toDateString(),
				'status' => EventStatus::pending->value,
			]);
		$event->appointments()->delete();
		Artisan::call('app:neb');
		expect(Appointment::count())->toBe(0);
	});

	it('cancels conflicting appointment and notifies holder', function () {
		$user = User::factory()->create();
		Event::factory()
			->hasDays(['day' => 'tuesday', 'start' => '10:00', 'end' => '12:00'])
			->create([
				'user_id' => $user->id,
				'start_date' => Carbon::now()->toDateString(),
				'status' => EventStatus::pending->value,
			]);
		$conflict = Appointment::factory()->create([
			'date' => Carbon::now()->toDateString(),
			'time' => '10:00',
			'status' => AppointmentStatus::accepted->value,
		]);
		Artisan::call('app:neb');
		$conflict->refresh();
		expect($conflict->status)->toBe(AppointmentStatus::canceled->value);
	});

	it('outputs confirmation message', function () {
		Artisan::call('app:neb');
		expect(Artisan::output())->toContain('Customers notified for the event begin.');
	});
});
