<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\AppointmentStatus;
use App\Enums\EventStatus;
use App\Enums\NotificationTypes;
use App\Models\Appointment;
use App\Models\Day;
use App\Models\Event;
use App\Services\AppointmentServices;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

final class NotifyEventBegun extends Command
{
    // app:notifiy-event-start
    protected $signature = 'app:neb {date? : the target date }';

    protected $description = 'Notify event customers about event begin';

    public function handle(): void
    {
        $argumentDate = $this->argument('date');

        if ($argumentDate) {
            $targetDate = Carbon::parse($argumentDate);
            $this->comment("Explicit date provided: {$targetDate->toDateString()}");
        } else {
            $daysInFuture = (int) Setting('days_before_event_start', 0);
            $targetDate = Carbon::now()->addDays($daysInFuture);
        }
        $events = Event::pending()->with(['user:id', 'customers', 'days'])
            ->whereBetween('start_date', [
                $targetDate->toDateString(),
                $targetDate->copy()->addDays()->toDateString(),
            ])
            ->lazy();

        $processed = 0;
        foreach ($events as $event) {
            $startDate = Carbon::parse($event->start_date);
            $diffInDays = (int) $targetDate->diff($startDate)->days;
            DB::transaction(
                function () use ($event, $targetDate, $diffInDays, &$processed) {
                    $processed++;
                    if ($diffInDays === 0) {
                        $this->setEventActiveWithFirstAppointment($event, $targetDate);
                    } elseif ($diffInDays === 1) {
                        $this->sendEventIsSoonNotifications($event);
                    }
                }
            );
        }
        $this->info("processed {$processed} events for {$targetDate->toDateString()}.");
    }

    private function setEventActiveWithFirstAppointment(Event $event, Carbon $date)
    {
        $dateString = $date->toDateString();
        $dayLabel = mb_strtolower($date->format('l'));

        $day = $event->days->firstWhere('day', $dayLabel);
        if (! $day) {
            $this->warn("Skipped event #{$event->id}: No schedule for a {$dayLabel}.");

            return;
        }
        $this->handleAppointmentConflict($dateString, $day, $event);
        app(AppointmentServices::class)->create(owner: $event, data: [
            'date' => $dateString,
            'time' => $day->start,
            'session_duration' => $this->sessionDurationInMinutes($day),
            'price' => $event->admission_fee,
            'notes' => 'event start',
        ]);

        $event->update(['status' => EventStatus::active->value]);

        $event->user->notify(...$this->eventStart($event));

        return $this->notifyCustomer($event, $this->eventStart($event));
    }

    private function sendEventIsSoonNotifications(Event $event)
    {
        $date = Carbon::createFromDate($event->start_date);
        $dayName = mb_strtolower($date->format('l'));
        $notification = $this->eventIsSoon($event, $dayName);
        $event->user->notify(...$notification);
        $this->notifyCustomer($event, $notification);
    }

    private function notifyCustomer($event, $notification)
    {
        foreach ($event->customers as $customer) {
            /** @var \App\Models\Customer $customer */
            $customer->notify(...$notification);
        }
    }

    private function handleAppointmentConflict(string $date, Day $day, Event $event): void
    {
        $appointment = app(AppointmentServices::class)
            ->getAppointmentIfExists([
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

        $appointment->holder->user->notify(...$this->conflict($event, $appointment));
    }

    private function eventIsSoon($event, $day): array
    {
        return [
            'Event Soon',
            "Your Event {$event->name} starts at $day.",
            ['type' => NotificationTypes::event->value, 'event' => $event->id],
        ];
    }

    private function eventStart($event): array
    {
        return [
            'Event Start',
            "Your Event {$event->name} starts today.",
            ['type' => NotificationTypes::event->value, 'event' => $event->id],
        ];
    }

    private function conflict(Event $event, Appointment $appointment): array
    {
        return [
            'Schedule Error',
            "You have schedule conflicts between '{$event->name}' and '{$appointment->holder->name}'",
            ['type' => NotificationTypes::normal->value],
        ];
    }

    private function sessionDurationInMinutes(Day $day): int
    {
        return (int) Carbon::parse($day->start)->diffInMinutes(Carbon::parse($day->end));
    }

    // public function handle(): void
    // {
    //     $now = Carbon::now();
    //     $events = Event::pending()->with([
    //         'user:id',
    //         'customers',
    //         'days',
    //     ])->lazy();
    //     foreach ($events as $event) {
    //         $eventDate = Carbon::createFromDate($event->start_date);
    //         $diffInDays = $now->diff($eventDate)->days;
    //         match ($diffInDays) {
    //             0 => $this->setEventActiveWithFirstAppointment($event, $now),
    //             1 => $this->sendEventIsSoonNotifications($event),
    //             default => $this->info('No Action needed for the upcoming event.'),
    //         };
    //     }
    //     $this->info('Customers notified for the event begin.');
    // }
}
