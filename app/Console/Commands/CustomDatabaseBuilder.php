<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

final class CustomDatabaseBuilder extends Command
{
    protected $signature = 'cdb
                            {--seed : Seed the database after running migrations}
                            {--seeder= : The class name of the root seeder}';

    protected $description = 'Wipes unprotected tables and runs migrations, safely skipping protected tables and their migration files.';

    /**
     * Define the database tables that must NEVER be dropped or re-migrated.
     */
    private array $protectedTables = [
        'tags',
        'categories',
        'settings',
        'admins',
        // 'taggables',
    ];

    public function handle(): int
    {
        if ($this->laravel->environment('production') && ! $this->confirm('Application In Production! Do you really want to run this?')) {
            return Command::FAILURE;
        }

        $this->components->info('Starting safe database reset sequence...');

        // 1. Drop only unprotected tables
        $allTables = Schema::getTables();

        if (! empty($allTables)) {
            Schema::disableForeignKeyConstraints();
            foreach ($allTables as $tableInfo) {
                $tableName = is_array($tableInfo) ? ($tableInfo['name'] ?? current($tableInfo)) : $tableInfo->name;

                if (in_array($tableName, $this->protectedTables, true)) {
                    $this->components->twoColumnDetail($tableName, '<fg=green>PROTECTED (SKIPPED DROP)</>');

                    continue;
                }

                Schema::drop($tableName);
                $this->components->twoColumnDetail($tableName, '<fg=red>DROPPED</>');
            }
            Schema::enableForeignKeyConstraints();
        }

        // 2. Identify migration files belonging to protected tables
        $this->comment('Analyzing migration paths to prevent execution conflicts...');
        $migrationFiles = glob(database_path('migrations/*.php'));
        $excludedPaths = [];

        foreach ($migrationFiles as $file) {
            $content = file_get_contents($file);
            foreach ($this->protectedTables as $protectedTable) {
                // Safely checks if the migration file targets the protected table
                if (Str::contains($content, ["Schema::create('{$protectedTable}'", "Schema::create(\"{$protectedTable}\""])) {
                    $excludedPaths[] = $file;
                    $this->components->twoColumnDetail(basename($file), '<fg=yellow>MIGRATION SKIPPED</>');
                }
            }
        }

        // 3. Run migrations selectively
        $this->comment('Running migrations for missing tables...');

        if (empty($excludedPaths)) {
            // No conflicts found, run normal migrations
            $this->call('migrate', ['--force' => true]);
        } else {
            // Re-run migrations one-by-one, skipping the excluded protected files
            foreach ($migrationFiles as $file) {
                if (in_array($file, $excludedPaths, true)) {
                    continue;
                }

                foreach ($this->protectedTables as $table) {
                    if (str_contains($file, $table)) {
                        continue;
                    }
                }

                $this->call('migrate', [
                    '--path' => 'database/migrations/'.basename($file),
                    '--force' => true,
                ]);
            }
        }

        // 4. Run optional seeding
        if ($this->option('seed')) {
            $this->comment('Running seeders...');
            $seederOptions = ['--force' => true];
            if ($this->option('seeder')) {
                $seederOptions['--class'] = $this->option('seeder');
            }
            $this->call('db:seed', $seederOptions);
        }

        $this->components->info('Safe migration sequence completed successfully.');

        return Command::SUCCESS;
    }
}
