<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\AppointmentStatus;
use App\Enums\NotificationTypes;
use App\Models\Appointment;
use App\Models\Course;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

final class NotifyCourseCustomer extends Command
{
    // app:notifiy-course-customer-session
    protected $signature = 'app:nccs';

    protected $description = 'Notify course customers about upcoming sessions';

    public function handle(): void
    {
        $now = Carbon::now();
        $dayName = mb_strtolower($now->format('l'));
        $date = $now->toDateString();

        $courses = Course::with([
            'user:id',
            'customers' => fn (BelongsToMany $query) => $query->where('is_complete', false),
            'days',
        ])->get();

        foreach ($courses as $course) {
            $day = $course->days->firstWhere('day', $dayName);
            if (! $day) {
                continue;
            }

            $this->handleAppointmentConflict($date, $day->start, $course);

            $appointment = $course->appointments()->create([
                'date' => $date,
                'time' => $day->start,
                'session_duration' => $course->session_duration,
                'status' => AppointmentStatus::accepted->value,
                'price' => ceil($course->price / $course->course_duration),
                'notes' => 'course session',
            ]);

            $course->user->notify(...$this->sessionNotification($course, $day));

            foreach ($course->customers as $customer) {
                $remaining = $customer->pivot->remaining_sessions;

                if ($remaining === 0 || (bool) $customer->pivot->is_complete === true) {
                    continue;
                }

                $isComplete = $remaining === 1;

                $customer->pivot->update([
                    'remaining_sessions' => $remaining - 1,
                    'is_complete' => $isComplete,
                ]);

                $customer->notify(...$this->sessionNotification($course, $day));

                if ($isComplete) {
                    $customer->notify(...$this->finishNotification($course));
                }
            }
        }

        $this->info('Customers notified for the upcoming session.');
    }

    private function handleAppointmentConflict(string $date, string $time, Course $course): void
    {
        $appointment = Appointment::where([
            ['date', $date],
            ['time', $time],
            ['status', AppointmentStatus::accepted->value],
        ])->first();

        if (! $appointment) {
            return;
        }

        $appointment->update([
            'status' => AppointmentStatus::canceled->value,
            'canceled_by' => class_basename($appointment->holder->user::class),
        ]);

        $appointment->holder->user->notify(
            'Schedule Error',
            "You have schedule conflicts between course '{$course->name}' and activity '{$appointment->holder->name}'",
            ['type' => NotificationTypes::normal->value]
        );
    }

    private function sessionNotification(Course $course, $day): array
    {
        return [
            'Course Session',
            "You have a {$course->name} session today at {$day->start}.",
            ['type' => NotificationTypes::session->value, 'course' => $course->id],
        ];
    }

    private function finishNotification(Course $course): array
    {
        return [
            'Course Finished',
            "Your course '{$course->name}' is now complete.",
            ['type' => NotificationTypes::course->value, 'course' => $course->id],
        ];
    }
}
