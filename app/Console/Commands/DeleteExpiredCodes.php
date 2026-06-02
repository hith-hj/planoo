<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

final class DeleteExpiredCodes extends Command
{
    // The name of the terminal command
    protected $signature = 'app:clean-expired-codes';

    // The description of the command
    protected $description = 'Delete all expired codes from the database';

    public function handle(): int
    {
        $deleted = DB::table('codes')
            ->where('expire_at', '<', now())
            ->delete();

        $this->info("Successfully deleted {$deleted} expired codes.");

        return Command::SUCCESS;
    }
}
