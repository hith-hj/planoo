<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\AppointmentStatus;
use App\Enums\NotificationTypes;
use App\Models\Appointment;
use App\Models\Course;
use App\Models\Day;
use App\Services\AppointmentServices;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

final class NotifyCourseCustomer extends Command
{
    protected $signature = 'app:nccs {date? : The target processing date (YYYY-MM-DD)}';

    protected $description = 'Notify course customers about upcoming sessions';

    public function handle(): void
    {
        $argumentDate = $this->argument('date');

        if ($argumentDate) {
            $targetDate = Carbon::parse($argumentDate);
        } else {
            $daysInFuture = (int) Setting('days_before_course_appointment', 0);
            $targetDate = Carbon::now()->addDays($daysInFuture);
        }

        $dayName = mb_strtolower($targetDate->format('l'));
        $dateString = $targetDate->toDateString();

        $courses = Course::with([
            'user:id',
            'customers' => fn (BelongsToMany $query) => $query->where('is_complete', false),
            'days',
        ])
            ->whereHas('days', fn ($query) => $query->where('day', $dayName))
            ->lazy();

        $appointmentService = app(AppointmentServices::class);

        foreach ($courses as $course) {
            $day = $course->days->firstWhere('day', $dayName);
            if (! $day) {
                continue;
            }

            DB::transaction(function () use ($course, $day, $dateString, $appointmentService) {
                $sessionDuration = $this->calculateSessionDurationFromDay($day);

                $this->handleAppointmentConflict($dateString, $day->start, $course, $sessionDuration, $appointmentService);

                $appointmentService->create(owner: $course, data: [
                    'date' => $dateString,
                    'time' => $day->start,
                    'session_duration' => $sessionDuration,
                    'price' => ceil($course->price / $course->course_duration),
                    'notes' => 'course session',
                ]);

                $course->user->notify(...$this->session($course, $day, $dateString));

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

                    $customer->notify(...$this->session($course, $day, $dateString));

                    if ($isComplete) {
                        $customer->notify(...$this->finish($course));
                    }
                }
            });
        }

        $this->info("Customers notified for sessions scheduled on {$dateString}.");
    }

    private function calculateSessionDurationFromDay(Day $day): int
    {
        return (int) Carbon::parse($day->start)->diffInMinutes(Carbon::parse($day->end));
    }

    private function handleAppointmentConflict(
        string $date,
        string $time,
        Course $course,
        int $session_duration,
        AppointmentServices $appointmentService
    ): void {
        $appointment = $appointmentService->getAppointmentIfExists([
            'date' => $date,
            'session_duration' => $session_duration,
            'time' => $time,
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

    private function session(Course $course, Day $day, string $dateString): array
    {
        $dayLabel = $dateString === Carbon::now()->toDateString() ? 'today' : "on {$dateString}";

        return [
            'Course Session',
            "You have a {$course->name} session {$dayLabel} at {$day->start}.",
            ['type' => NotificationTypes::session->value, 'course' => $course->id],
        ];
    }

    private function finish(Course $course): array
    {
        return [
            'Course Finished',
            "Your course '{$course->name}' is now complete.",
            ['type' => NotificationTypes::course->value, 'course' => $course->id],
        ];
    }

    private function conflict(Course $course, Appointment $appointment): array
    {
        return [
            'Schedule Error',
            "You have schedule conflicts between course '{$course->name}' and activity '{$appointment->holder->name}'",
            ['type' => NotificationTypes::normal->value],
        ];
    }
}
