<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\AppointmentStatus;
use App\Enums\NotificationTypes;
use App\Models\Appointment;
use App\Models\Course;
use Carbon\Carbon;
use Illuminate\Console\Command;

final class NotifyCourseCustomer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // protected $signature = 'app:notify-course-customer';
    protected $signature = 'app:nccs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify course customer for the upcomming session';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $Day = mb_strtolower($now->format('l'));
        $Date = $now->toDateString();
        $courses = Course::with(['user:id', 'customers:id', 'days'])->get();
        foreach ($courses as $course) {
            $day = $course->days()->where('day', $Day)->first();
            if ($day === null) {
                continue;
            }
            $this->checkIfAppointmentExists($Date, $day, $course);
            $course->appointments()->create([
                'date' => $Date,
                'time' => $day->start,
                'session_duration' => $course->session_duration,
                'status' => AppointmentStatus::accepted->value,
                'price' => $course->price,
                'notes' => $data['notes'] ?? null,
            ]);
            $notification = [
                'Course session',
                "you have {$course->name} session today at {$day->start}",
                ['type' => NotificationTypes::session->value, 'course' => $course->id],
            ];
            $course->user->notify(...$notification);
            foreach ($course->customers as $customer) {
                $customer->notify(...$notification);
            }
        }
        $this->info('Customers notified for the upcomming session');
    }

    private function checkIfAppointmentExists($date, $day, $course)
    {
        $appointment = Appointment::where([
            ['date',  $date],
            ['time', $day->start],
            ['status', AppointmentStatus::accepted->value],
        ]);
        if (! $appointment->exists()) {
            return;
        }
        $appointment = $appointment->first();
        $appointment->update([
            'status' => AppointmentStatus::canceled->value,
            'canceled_by' => class_basename($appointment->holder->user::class),
        ]);
        $appointment->holder->user->notify(
            'Schedule Error',
            "You have Schedule conflicts, between course:'{$course->name}', activity: '{$appointment->holder->name}'",
            ['type' => NotificationTypes::normal->value]
        );
    }
}
