<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;

final class ResourceConflictDetectorJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected string $resource_type,
        protected int $resource_id
    ) {}

    public function handle(): void
    {
        // executes command resource:detect-conflict with resource-type resource-id
        Artisan::call('resource:detect-conflict', [
            '--resource-type' => $this->resource_type,
            '--resource-id' => $this->resource_id,
        ]);
    }
}
