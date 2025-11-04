<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\AppointmentStatus;
use App\Enums\EventStatus;
use App\Enums\NotificationTypes;
use App\Models\Appointment;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Console\Command;

final class NotifyEventStart extends Command
{
    // app:notifiy-event-start
    protected $signature = 'app:neb';

    protected $description = 'Notify event customers about event begin';

    public function handle()
    {
        $now = Carbon::now();
        $events = Event::pending()->with([
            'user:id',
            'customers',
            'days',
        ])->get();

        foreach ($events as $event) {
            $eventDate = Carbon::createFromDate($event->start_date);
            $diffInDays = $now->diff($eventDate)->days;
            match ($diffInDays) {
                0 => $this->setEventActiveWithFirstAppointment($event, $now),
                1 => $this->sendEventIsSoonNotifications($event),
                default => $this->info('No Action needed for the upcoming event.'),
            };
        }
        $this->info('Customers notified for the event begin.');

    }

    private function setEventActiveWithFirstAppointment(Event $event, Carbon $date)
    {
        $dateString = $date->toDateString();
        $event->user->notify(...$this->eventStart($event));
        $day = $event->days->firstWhere('day', mb_strtolower($date->format('l')));
        if (! $day) {
            $this->error('no matching days');

            return;
        }
        $this->handleAppointmentConflict($dateString, $day->start, $event);

        $event->appointments()->create([
            'date' => $dateString,
            'time' => $day->start,
            'session_duration' => $this->sessionDurationInMinutes($day),
            'status' => AppointmentStatus::accepted->value,
            'price' => $event->admission_fee,
            'notes' => 'event start',
        ]);

        $event->update(['status' => EventStatus::active->value]);

        return $this->notifyCustomer($event, $this->eventStart($event));
    }

    private function sendEventIsSoonNotifications(Event $event)
    {
        $date = Carbon::createFromDate($event->start_date);
        $dayName = mb_strtolower($date->format('l'));
        $event->user->notify(...$this->eventIsSoon($event, $dayName));

        return $this->notifyCustomer($event, $this->eventIsSoon($event, $dayName));
    }

    private function notifyCustomer($event, $notification)
    {
        foreach ($event->customers as $customer) {
            $customer->notify(...$notification);
        }
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

    private function eventIsSoon($event, $day)
    {
        return [
            'Event Soon',
            "Your Event {$event->name} start at $day.",
            ['type' => NotificationTypes::event->value, 'event' => $event->id],
        ];
    }

    private function eventStart($event)
    {
        return [
            'Event Start',
            "Your Event {$event->name} start today.",
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
