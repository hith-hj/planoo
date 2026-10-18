<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\SyncMediaFile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

final class SyncMediaToPublic extends Command
{
    protected $signature = 'app:sync-files-to-public {path? : A disk-relative uploads/... file}';

    protected $description = 'Queue media mirroring for one file or all uploaded files';

    public function handle(): int
    {
        $path = $this->argument('path');
        if ($path !== null) {
            SyncMediaFile::dispatch($path);
            $this->info('Media file queued for synchronization.');

            return self::SUCCESS;
        }
        $count = 0;
        foreach (Storage::disk('public')->getDriver()->listContents('uploads', true) as $item) {
            if ($item->isFile()) {
                SyncMediaFile::dispatch($item->path());
                $count++;
            }
        }
        $this->info("Queued {$count} media files for synchronization.");

        return self::SUCCESS;
    }
}
