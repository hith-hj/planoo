<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Console\Command;

final class CheckCompletedAppointments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:check-completed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark past accepted appointment as completed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $currentDate = $now->toDateString();
        $currentHour = $now->format('H:i');
        Appointment::where([
            ['date', '<=', $currentDate],
            ['time', '<', $currentHour],
            ['status', AppointmentStatus::accepted->value],
        ])->update(['status' => AppointmentStatus::completed->value]);

        $this->info('Past accepted appointments marked as completed.');
    }
}
