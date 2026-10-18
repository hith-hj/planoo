<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Console\Command;

final class CheckCompletedAppointments extends Command
{
    // app:check-appointments-completed
    protected $signature = 'app:cac';

    protected $description = 'Mark past accepted appointment as completed';

    public function handle()
    {
        $now = Carbon::now();
        $currentDate = $now->toDateString();
        $currentHour = $now->format('H:i');
        Appointment::query()
            ->where('status', AppointmentStatus::accepted->value)
            ->where(function ($query) use ($currentDate, $currentHour): void {
                $query->where('date', '<', $currentDate)
                    ->orWhere(function ($query) use ($currentDate, $currentHour): void {
                        $query->where('date', $currentDate)->where('end_at', '<=', $currentHour.':00');
                    });
            })
            ->with('holder.user')
            ->chunkById(100, function ($appointments): void {
                foreach ($appointments as $appointment) {
                    $appointment->update(['status' => AppointmentStatus::completed->value]);
                }
            });

        $this->info('Past accepted appointments marked as completed.');
    }
}
