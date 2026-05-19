<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;

final class SetSystemSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:set-system-settings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $settings = [
            [
                'name' => 'course_cancelation_period',
                'value' => 24,
                'description' => 'The period after which course cancelation is not allowed (number in hours)',
            ],
            [
                'name' => 'appointment_cancelation_period',
                'value' => 1,
                'description' => 'The duration after which the appointment cancelation is not allowed (number in hours)',
            ],
            [
                'name' => 'event_cancelation_period',
                'value' => 24,
                'description' => 'The period after which event cancelation is not allowed (number in hours)',
            ],
            [
                'name' => 'max_media_count',
                'value' => 5,
                'description' => 'Max allowed number for media upload',
            ],
            [
                'name' => 'course_capacity',
                'value' => 30,
                'description' => 'This is the maximum number of customer that can attend the course',
            ],
            [
                'name' => 'event_capacity',
                'value' => 30,
                'description' => 'This is the maximum number of customer that can attend the event',
            ],
            [
                'name' => 'event_duration',
                'value' => 30,
                'description' => 'The max duration allowed for an event (number in days)',
            ],
        ];

        return Setting::insert($settings);
    }
}
