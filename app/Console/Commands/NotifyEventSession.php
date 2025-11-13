<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\AppointmentStatus;
use App\Enums\EventStatus;
use App\Enums\NotificationTypes;
use App\Models\Appointment;
use App\Models\Event;
use App\Services\AppointmentServices;
use Carbon\Carbon;
use Illuminate\Console\Command;

final class NotifyEventSession extends Command
{
    // app:notify-event-session
    protected $signature = 'app:nes';

    protected $description = 'Notify event customers about event session';

    public function handle(): void
    {
        $now = Carbon::now();
        $dayName = mb_strtolower($now->format('l'));
        $date = $now->toDateString();

        $events = Event::active()->with([
            'user:id',
            'customers',
            'days',
        ])->lazy();
        foreach ($events as $event) {
            $day = $event->days->firstWhere('day', $dayName);
            if (! $day) {
                continue;
            }
            $this->handleAppointmentConflict($date, $day->start, $event);
            $data = [
                'date' => $date,
                'time' => $day->start,
                'session_duration' => $this->sessionDurationInMinutes($day),
                'price' => $event->admission_fee,
                'notes' => 'event session',
            ];
            app(AppointmentServices::class)->create(owner: $event, data: $data);
            $notification = $this->sessionNotification($event, $day);
            if ($event->end_date === $date) {
                $event->update(['status' => EventStatus::completed->value]);
                $notification = $this->finishNotification($event, $day);
            }

            $event->user->notify(...$notification);
            foreach ($event->customers as $customer) {
                $customer->notify(...$notification);
            }
        }

        $this->info('Customers notified for the upcoming event session.');
    }

    private function handleAppointmentConflict(string $date, string $time, Event $event): void
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
            "You have schedule conflicts between event '{$event->name}' and activity '{$appointment->holder->name}'",
            ['type' => NotificationTypes::normal->value]
        );
    }

    private function sessionNotification(Event $event, $day): array
    {
        return [
            'Event Session',
            "You have a {$event->name} session today at {$day->start}.",
            ['type' => NotificationTypes::session->value, 'event' => $event->id],
        ];
    }

    private function finishNotification(Event $event): array
    {
        return [
            'Event Finished',
            "Your event '{$event->name}' is now complete.",
            ['type' => NotificationTypes::event->value, 'event' => $event->id],
        ];
    }

    private function sessionDurationInMinutes($day)
    {
        $dayStart = Carbon::createFromTimeString($day->start);
        $dayEnd = Carbon::createFromTimeString($day->end);

        return $dayStart->diff($dayEnd)->hours * 60;
    }
}
