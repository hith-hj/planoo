<?php

declare(strict_types=1);

namespace App\Observers;

use App\Enums\AppointmentStatus;
use App\Enums\NotificationTypes;
use App\Models\Appointment;
use App\Models\Customer;
use App\Models\User;

final class AppointmentObserver
{
    public function created(Appointment $appointment): void
    {
        $this->accepted($appointment);
    }

    public function accepted(Appointment $appointment)
    {
        $appointment->customer?->notify(
            'Appointment accepted',
            'Your Appointment has been accepted',
            ['type' => NotificationTypes::appointment->value, 'appointment' => $appointment->id]
        );
    }

    public function updated(Appointment $appointment): void
    {
        match ($appointment->status) {
            AppointmentStatus::completed->value => $this->completed($appointment),
            AppointmentStatus::canceled->value => $this->canceled($appointment),
            default => true,
        };
    }

    public function completed(Appointment $appointment)
    {
        $appointment->holder?->user?->notify(
            'Appointment completed',
            'An Appointment has been completed',
            ['type' => NotificationTypes::appointment->value, 'appointment' => $appointment->id]
        );
    }

    public function canceled(Appointment $appointment)
    {
        $toNotify = match ($appointment->canceled_by) {
            class_basename(User::class) => $appointment->customer,
            class_basename(Customer::class) => $appointment->holder->user,
            default => null,
        };
        if ($toNotify !== null) {
            $toNotify?->notify(
                'Appointment canceled',
                'An Appointment has been canceled',
                ['type' => NotificationTypes::appointment->value, 'appointment' => $appointment->id]
            );
        }
    }
}
