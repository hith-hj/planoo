<?php

declare(strict_types=1);

use App\Enums\AppointmentStatus;
use App\Jobs\DeleteMediaFile;
use App\Models\Activity;
use App\Models\Appointment;
use App\Models\Course;
use App\Models\Customer;
use App\Models\Event;
use App\Models\Notification;
use App\Models\User;
use App\Services\AppointmentServices;
use App\Services\CustomerServices;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->seed();
});

it('deletes only the customer and their enrollments, preserving shared resources', function () {
    $customer = Customer::factory()->create();
    $other = Customer::factory()->create();
    $course = Course::firstOrFail();
    $event = Event::firstOrFail();
    foreach ([$customer, $other] as $attendee) {
        $attendee->courses()->attach($course->id, ['remaining_sessions' => 1, 'is_complete' => false]);
        $attendee->events()->attach($event->id);
    }
    $session = $course->appointments()->firstOrFail();
    $course->update(['capacity' => $course->customers()->count(), 'is_full' => true]);
    $event->update(['capacity' => $event->customers()->count(), 'is_full' => true]);

    expect(app(CustomerServices::class)->delete($customer))->toBeTrue();

    $this->assertModelMissing($customer);
    $this->assertModelExists($course);
    $this->assertModelExists($event);
    $this->assertModelExists($session);
    expect($course->fresh()->is_full)->toBeFalse();
    expect($event->fresh()->is_full)->toBeFalse();
    $this->assertDatabaseMissing('course_customer', ['customer_id' => $customer->id]);
    $this->assertDatabaseMissing('customer_event', ['customer_id' => $customer->id]);
    $this->assertDatabaseHas('course_customer', ['customer_id' => $other->id, 'course_id' => $course->id]);
    $this->assertDatabaseHas('customer_event', ['customer_id' => $other->id, 'event_id' => $event->id]);
});

it('rolls back enrollment removal when deleting the account fails', function () {
    $real = Customer::factory()->create();
    $course = Course::firstOrFail();
    $real->courses()->attach($course->id, ['remaining_sessions' => 1, 'is_complete' => false]);
    $course->update(['capacity' => $course->customers()->count(), 'is_full' => true]);
    Storage::fake('public');
    $path = 'uploads/images/account.png';
    Storage::disk('public')->put($path, 'keep on rollback');
    $media = $real->medias()->create(['url' => $path, 'type' => 'image', 'name' => 'profile_image']);
    Bus::fake([DeleteMediaFile::class]);
    DB::connection()->beforeExecuting(function (string $sql): void {
        if (preg_match('/^delete from ["`]?customers["`]? /i', $sql)) {
            throw new RuntimeException('Deletion failed');
        }
    });

    expect(fn () => app(CustomerServices::class)->delete($real))->toThrow(RuntimeException::class);
    $this->assertDatabaseHas('customers', ['id' => $real->id]);
    $this->assertDatabaseHas('course_customer', ['customer_id' => $real->id, 'course_id' => $course->id]);
    expect($course->fresh()->is_full)->toBeTrue();
    $this->assertModelExists($media);
    Storage::disk('public')->assertExists($path);
    Bus::assertNotDispatched(DeleteMediaFile::class);
});

it('removes customer uploads after commit and synchronizes their deletion', function () {
    Storage::fake('public');
    Storage::fake('media_mirror');
    $customer = Customer::factory()->create();
    $path = 'uploads/images/deleted-account.png';
    foreach (['public', 'media_mirror'] as $disk) {
        Storage::disk($disk)->put($path, 'old image');
        Storage::disk($disk)->put('uploads/images/unrelated.png', 'keep');
    }
    $media = $customer->medias()->create(['url' => $path, 'type' => 'image', 'name' => 'profile_image']);
    Bus::fake([DeleteMediaFile::class]);
    app(CustomerServices::class)->delete($customer);
    $this->assertModelMissing($media);
    Bus::assertDispatched(DeleteMediaFile::class, fn ($job) => $job->path === $path && $job->afterCommit);
    app()->instance('env', 'production');
    try {
        (new DeleteMediaFile($path))->handle();
        (new DeleteMediaFile($path))->handle();
    } finally {
        app()->instance('env', 'testing');
    }
    foreach (['public', 'media_mirror'] as $disk) {
        Storage::disk($disk)->assertMissing($path);
        Storage::disk($disk)->assertExists('uploads/images/unrelated.png');
    }
});

it('rejects reading and cancelling another customer appointment', function () {
    $this->user('customer')->api();
    $appointment = Appointment::factory()->for(Activity::firstOrFail(), 'holder')->create();
    $this->getJson(route('customer.appointment.find', ['appointment_id' => $appointment->id]))->assertNotFound();
    $this->postJson(route('customer.appointment.cancel'), ['appointment_id' => $appointment->id])->assertNotFound();
    expect($appointment->fresh()->status)->toBe(AppointmentStatus::accepted->value);
});

it('rejects reading and cancelling another partner appointment', function () {
    $partner = User::firstOrFail();
    $other = User::whereKeyNot($partner->id)->firstOrFail();
    $this->replaceUser($partner);
    $activity = $other->activities()->firstOrFail();
    $appointment = Appointment::factory()->for($activity, 'holder')->create();
    $this->getJson(route('partner.appointment.find', ['appointment_id' => $appointment->id]))->assertNotFound();
    $this->postJson(route('partner.appointment.cancel'), ['appointment_id' => $appointment->id])->assertNotFound();
    expect($appointment->fresh()->status)->toBe(AppointmentStatus::accepted->value);
});

it('allows attendees to read their shared course and event sessions', function () {
    $customer = Customer::factory()->create();
    $course = Course::firstOrFail();
    $event = Event::firstOrFail();
    $customer->courses()->attach($course->id, ['remaining_sessions' => 1, 'is_complete' => false]);
    $customer->events()->attach($event->id);
    foreach ([$course, $event] as $owner) {
        $session = $owner->appointments()->firstOrFail();
        expect(app(AppointmentServices::class)->findForActor($customer, $session->id)->id)->toBe($session->id);
    }
});

it('scopes notification updates to the authenticated recipient', function (string $role) {
    $this->user($role, 'stadium')->api();
    $owned = Notification::factory()->for($this->user, 'holder')->create(['is_viewed' => false]);
    $other = Notification::factory()->for(Customer::factory()->create(), 'holder')->create(['is_viewed' => false]);
    $this->postJson(route($role.'.notification.view'), ['notifications' => [$owned->id, $other->id]])->assertOk();
    expect($owned->fresh()->is_viewed)->toBeTrue();
    expect($other->fresh()->is_viewed)->toBeFalse();
})->with(['customer', 'partner']);

it('detects conflicts with even a single enrolled group session', function (string $type) {
    $customer = Customer::factory()->create();
    $owner = $type === 'course' ? Course::firstOrFail() : Event::firstOrFail();
    if ($type === 'course') {
        $customer->courses()->attach($owner->id, ['remaining_sessions' => 1, 'is_complete' => false]);
    } else {
        $customer->events()->attach($owner->id);
    }
    $owner->appointments()->delete();
    $session = Appointment::factory()->for($owner, 'holder')->create([
        'date' => now()->addWeek()->toDateString(),
        'time' => '10:00',
        'end_at' => '11:00',
        'session_duration' => 60,
        'status' => AppointmentStatus::accepted->value,
    ]);
    $data = ['date' => $session->date, 'time' => '10:30', 'session_duration' => 60];
    expect(app(AppointmentServices::class)->canCreateAppointment($customer, $data))->toBeFalse();
    $session->update(['status' => AppointmentStatus::canceled->value]);
    expect(app(AppointmentServices::class)->canCreateAppointment($customer, $data))->toBeTrue();
})->with(['course', 'event']);
