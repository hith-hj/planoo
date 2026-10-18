<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

final class DeleteMediaFile implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 60;

    public function __construct(public string $path)
    {
        new SyncMediaFile($path);
        $this->afterCommit();
    }

    public function backoff(): array
    {
        return [30, 120, 300];
    }

    public function handle(): void
    {
        $disk = Storage::disk('public');
        if ($disk->exists($this->path) && ! $disk->delete($this->path)) {
            throw new RuntimeException('Failed to delete customer media.');
        }
        if (! app()->environment(['local', 'testing'])) {
            SyncMediaFile::dispatch($this->path);
        }
    }
}
