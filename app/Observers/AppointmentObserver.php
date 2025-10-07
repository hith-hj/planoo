<?php

namespace App\Observers;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\User;

class AppointmentObserver
{

    public function created(Appointment $appointment): void
    {
        $this->accepted($appointment);
    }

    public function accepted(Appointment $appointment)
    {
        // $appointment->customer->notify();
    }

    public function updated(Appointment $appointment): void
    {
        match($appointment->status){
            AppointmentStatus::completed->value => $this->completed($appointment),
            AppointmentStatus::canceled->value => $this->canceled($appointment),
            default => true,
        };
    }

    public function completed(Appointment $appointment)
    {
        $appointment->holder->user->notify();
    }

    public function canceled(Appointment $appointment)
    {
        match($appointment->canceled_by){
            class_basename(User::class) => $appointment->holder->user->notify(),
            // class_basename(Customer::class) => $appointment->customer->notify(),
            default => true,
        };
    }

}
