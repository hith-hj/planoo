<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use RuntimeException;

final class SyncMediaFile implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 60;

    public function __construct(public string $path)
    {
        if (! str_starts_with($path, 'uploads/') || str_contains($path, '\\') || str_contains($path, "\0")
            || array_intersect(explode('/', $path), ['', '.', '..']) !== []) {
            throw new InvalidArgumentException('Invalid media sync path.');
        }
        $this->afterCommit();
    }

    public function backoff(): array
    {
        return [30, 120, 300];
    }

    public function middleware(): array
    {
        return [(new WithoutOverlapping('media-sync:'.hash('sha256', $this->path)))
            ->releaseAfter(60)->expireAfter(90)];
    }

    public function handle(): void
    {
        $source = Storage::disk('public');
        $destination = Storage::disk('media_mirror');
        if ($source->path($this->path) === $destination->path($this->path)) {
            return;
        }
        if (! $source->exists($this->path)) {
            if ($destination->exists($this->path) && ! $destination->delete($this->path)) {
                throw new RuntimeException('Failed to remove mirrored media.');
            }

            return;
        }
        $stream = $source->readStream($this->path);
        if (! is_resource($stream)) {
            throw new RuntimeException('Failed to read media for synchronization.');
        }
        try {
            if (! $destination->put($this->path, $stream)) {
                throw new RuntimeException('Failed to synchronize media.');
            }
        } finally {
            fclose($stream);
        }
    }
}
