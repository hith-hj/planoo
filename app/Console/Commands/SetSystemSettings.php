<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

final class SetSystemSettings extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:set-system-settings {--name=} {--input=} {--description=} {--force}';

    /**
     * The console command description.
     */
    protected $description = 'Seed default system settings into the database or create an individual setting.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        if (! Schema::hasTable('settings')) {
            $this->fail('Settings table is missing');
        }

        $name = $this->option('name');
        $value = $this->option('input');
        $description = $this->option('description');

        if ($name !== null && $value !== null) {
            $this->info('Input detected, creating or finding setting...');

            Setting::firstOrCreate(
                ['name' => $name],
                ['value' => $value, 'description' => $description]
            );

            $this->info("Setting '{$name}' is ready.");

            return;
        }

        $this->bulkSeeding();
        $this->info('Finished processing settings.');
    }

    private function bulkSeeding(): void
    {
        $this->info('Checking system settings...');

        if ($this->option('force')) {
            $this->warn('Force detected. Overwriting settings back to defaults...');
            Setting::upsert($this->systemSettings(), ['name'], ['value', 'description']);

            return;
        }

        $insertedCount = 0;
        foreach ($this->systemSettings() as $defaultSetting) {
            $setting = Setting::firstOrCreate(
                ['name' => $defaultSetting['name']],
                [
                    'value' => $defaultSetting['value'],
                    'description' => $defaultSetting['description'],
                ]
            );

            if ($setting->wasRecentlyCreated) {
                $insertedCount++;
                $this->line("Added missing setting: <info>{$defaultSetting['name']}</info>");
            }
        }

        if ($insertedCount === 0) {
            $this->info('All settings already exist. No modifications made to your database.');
            $this->comment('Tip: Use --force if you want to explicitly overwrite data back to defaults.');
        } else {
            $this->info("Successfully added {$insertedCount} missing settings.");
        }
    }

    private function systemSettings(): array
    {
        return [
            [
                'name' => 'course_cancelation_period',
                'value' => '24',
                'description' => 'The period after which course cancelation is not allowed (number in hours)',
            ],
            [
                'name' => 'days_before_course_start',
                'value' => '0',
                'description' => 'This controll when the course starting notification is sent',
            ],
            [
                'name' => 'days_before_course_appointment',
                'value' => '0',
                'description' => 'This controll when the appointment of the course is created',
            ],
            [
                'name' => 'days_before_event_start',
                'value' => '0',
                'description' => 'This controll when the event starting notification is sent',
            ],
            [
                'name' => 'days_before_event_appointment',
                'value' => '0',
                'description' => 'This controll when the appointment of the event is created',
            ],
            [
                'name' => 'appointment_cancelation_period',
                'value' => '1',
                'description' => 'The duration after which the appointment cancelation is not allowed (number in hours)',
            ],
            [
                'name' => 'event_cancelation_period',
                'value' => '24',
                'description' => 'The period after which event cancelation is not allowed (number in hours)',
            ],
            [
                'name' => 'max_media_count',
                'value' => '5',
                'description' => 'Max allowed number for media upload',
            ],
            [
                'name' => 'course_capacity',
                'value' => '30',
                'description' => 'This is the maximum number of customer that can attend the course (number for customers)',
            ],
            [
                'name' => 'event_capacity',
                'value' => '30',
                'description' => 'This is the maximum number of customer that can attend the event (number for customers)',
            ],
            [
                'name' => 'event_duration',
                'value' => '30',
                'description' => 'The max duration allowed for an event (number in days)',
            ],
            [
                'name' => 'max_code_generation_attempts',
                'value' => '5',
                'description' => 'the maximum number of code generation attempts',
            ],
            [
                'name' => 'generated_code_length',
                'value' => '5',
                'description' => 'the number of generated code digits',
            ],
            [
                'name' => 'minimum_customer_age',
                'value' => '14',
                'description' => 'the minimum age required for customer to register',
            ],
        ];
    }
}
