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

final class NotifyEventSession extends Command
{
    protected $signature = 'app:nes {date? : The target processing date (YYYY-MM-DD)}';

    protected $description = 'Notify event customers about event session';

    public function handle(): void
    {
        $argumentDate = $this->argument('date');

        if ($argumentDate) {
            $targetDate = Carbon::parse($argumentDate);
            $this->comment("Explicit date provided: {$targetDate->toDateString()}");
        } else {
            $daysInFuture = (int) Setting('days_before_event_appointment', 0);
            $targetDate = Carbon::now()->addDays($daysInFuture);
            $this->comment("No date provided. Using dashboard offset (+{$daysInFuture} days): {$targetDate->toDateString()}");
        }

        $dayName = mb_strtolower($targetDate->format('l'));
        $dateString = $targetDate->toDateString();

        $events = Event::active()
            ->with([
                'user:id',
                'customers',
                'days',
            ])
            ->whereHas('days', fn ($query) => $query->where('day', $dayName))
            ->lazy();

        $appointmentService = app(AppointmentServices::class);

        foreach ($events as $event) {
            $day = $event->days->firstWhere('day', $dayName);
            if (! $day) {
                continue;
            }

            DB::transaction(function () use ($event, $day, $dateString, $appointmentService) {
                $sessionDuration = $this->sessionDurationInMinutes($day);

                $this->handleAppointmentConflict($dateString, $day, $event, $sessionDuration, $appointmentService);

                $appointmentService->create(owner: $event, data: [
                    'date' => $dateString,
                    'time' => $day->start,
                    'session_duration' => $sessionDuration,
                    'price' => $event->admission_fee,
                    'notes' => 'event session',
                ]);

                $notification = $this->session($event, $day, $dateString);

                if ($event->end_date === $dateString) {
                    $event->update(['status' => EventStatus::completed->value]);
                    $notification = $this->finish($event);
                }

                $event->user->notify(...$notification);
                foreach ($event->customers as $customer) {
                    $customer->notify(...$notification);
                }
            });
        }

        $this->info("Customers notified for upcoming event sessions on {$dateString}.");
    }

    private function handleAppointmentConflict(
        string $date,
        Day $day,
        Event $event,
        int $sessionDuration,
        AppointmentServices $appointmentService
    ): void {
        $appointment = $appointmentService->getAppointmentIfExists($event, [
            'date' => $date,
            'session_duration' => $sessionDuration,
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

    private function session(Event $event, Day $day, string $dateString): array
    {
        $dayLabel = $dateString === Carbon::now()->toDateString() ? 'today' : "on {$dateString}";

        return [
            'Event Session',
            "You have a {$event->name} session {$dayLabel} at {$day->start}.",
            ['type' => NotificationTypes::session->value, 'event' => $event->id],
        ];
    }

    private function finish(Event $event): array
    {
        return [
            'Event Finished',
            "Your event '{$event->name}' is now complete.",
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
}
