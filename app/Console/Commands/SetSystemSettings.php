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
        $this->info('Finished inserting.');
    }

    private function bulkSeeding(): void
    {
        $this->info('Checking if settings exist...');

        if (Setting::exists()) {
            $this->info('Settings are already inserted.');

            if (! $this->option('force')) {
                $this->warn('Execution stopped. Use --force to overwrite or append.');
                return;
            }

            $this->warn('Force flag detected. Proceeding...');
        }

        $this->info('Inserting system settings into the database...');

        Setting::upsert($this->systemSettings(), ['name'], ['value', 'description']);
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
        ];
    }
}
