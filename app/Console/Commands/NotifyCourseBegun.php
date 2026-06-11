<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\AppointmentStatus;
use App\Enums\CourseStatus;
use App\Enums\NotificationTypes;
use App\Models\Appointment;
use App\Models\Course;
use App\Models\Day;
use App\Services\AppointmentServices;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

final class NotifyCourseBegun extends Command
{
    // app:notifiy-course-start
    protected $signature = 'app:ncb {date? : the target date }';

    protected $description = 'Notify course customers about course begun';

    public function handle(): void
    {
        $argumentDate = $this->argument('date');

        if ($argumentDate) {
            $targetDate = Carbon::parse($argumentDate);
            $this->comment("Explicit date provided: {$targetDate->toDateString()}");
        } else {
            $daysInFuture = (int) Setting('days_before_course_start', 0);
            $targetDate = Carbon::now()->addDays($daysInFuture);
        }
        $courses = Course::pending()->with(['user:id', 'customers', 'days'])
            ->whereBetween('start_date', [
                $targetDate->toDateString(),
                $targetDate->copy()->addDays()->toDateString(),
            ])
            ->lazy();

        $processed = 0;
        foreach ($courses as $course) {
            $startDate = Carbon::parse($course->start_date);
            $diffInDays = (int) $targetDate->diff($startDate)->days;
            DB::transaction(
                function () use ($course, $targetDate, $diffInDays, &$processed) {
                    $processed++;
                    if ($diffInDays === 0) {
                        $this->setCourseActiveWithFirstAppointment($course, $targetDate);
                    } elseif ($diffInDays === 1) {
                        $this->sendCourseIsSoonNotifications($course);
                    }
                }
            );
        }
        $this->info("processed {$processed} courses for {$targetDate->toDateString()}.");
    }

    private function setCourseActiveWithFirstAppointment(Course $course, Carbon $date)
    {
        $dateString = $date->toDateString();
        $dayLabel = mb_strtolower($date->format('l'));

        $day = $course->days->firstWhere('day', $dayLabel);
        if (! $day) {
            $this->warn("Skipped course #{$course->id}: No schedule for a {$dayLabel}.");

            return;
        }
        $this->handleAppointmentConflict($dateString, $day, $course);
        app(AppointmentServices::class)->create(owner: $course, data: [
            'date' => $dateString,
            'time' => $day->start,
            'session_duration' => $this->sessionDurationInMinutes($day),
            'price' => ceil($course->price / $course->course_duration),
            'notes' => 'course start',
        ]);

        $course->update(['status' => CourseStatus::active->value]);

        $course->user->notify(...$this->courseStart($course));

        foreach ($course->customers as $customer) {
            $pivot = $customer->pivot;
            $remaining = $pivot->remaining_sessions;

            if ($remaining <= 0 || (bool) $pivot->is_complete === true) {
                continue;
            }

            $newRemaining = $remaining - 1;
            $isComplete = ($newRemaining === 0);

            $pivot->update([
                'remaining_sessions' => $newRemaining,
                'is_complete' => $isComplete,
            ]);

            $customer->notify(...$this->courseStart($course, $day, $dateString));
        }
    }

    private function sendCourseIsSoonNotifications(Course $course)
    {
        $date = Carbon::createFromDate($course->start_date);
        $dayName = mb_strtolower($date->format('l'));
        $notification = $this->courseIsSoon($course, $dayName);
        $course->user->notify(...$notification);
        $this->notifyCustomer($course, $notification);
    }

    private function notifyCustomer(Course $course, array $notification)
    {
        foreach ($course->customers as $customer) {
            /** @var \App\Models\Customer $customer */
            $customer->notify(...$notification);
        }
    }

    private function handleAppointmentConflict(string $date, Day $day, Course $course): void
    {
        $appointment = app(AppointmentServices::class)
            ->getAppointmentIfExists($course, [
                'date' => $date,
                'session_duration' => $this->sessionDurationInMinutes($day),
                'time' => $day->start,
            ]);

        if (! $appointment) {
            return;
        }

        $appointment->update([
            'status' => AppointmentStatus::canceled->value,
            'canceled_by' => class_basename($appointment->holder->user::class),
        ]);

        $appointment->holder->user->notify(...$this->conflict($course, $appointment));
    }

    private function courseIsSoon(Course $course, string $day): array
    {
        return [
            'Course Soon',
            "Your Course {$course->name} starts at $day.",
            ['type' => NotificationTypes::course->value, 'course' => $course->id],
        ];
    }

    private function courseStart(Course $course): array
    {
        return [
            'Course Start',
            "Your Course {$course->name} started",
            ['type' => NotificationTypes::course->value, 'course' => $course->id],
        ];
    }

    private function conflict(Course $course, Appointment $appointment): array
    {
        return [
            'Schedule Error',
            "You have schedule conflicts between '{$course->name}' and '{$appointment->holder->name}'",
            ['type' => NotificationTypes::normal->value],
        ];
    }

    private function sessionDurationInMinutes(Day $day): int
    {
        return (int) Carbon::parse($day->start)->diffInMinutes(Carbon::parse($day->end));
    }
}
