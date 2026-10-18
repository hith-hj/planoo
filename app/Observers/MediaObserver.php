<?php

declare(strict_types=1);

namespace App\Observers;

use App\Jobs\SyncMediaFile;
use App\Models\Media;

final class MediaObserver
{
    public function deleted(Media $media): void
    {
        if (! app()->environment(['local', 'testing']) && str_starts_with($media->url, 'uploads/')) {
            SyncMediaFile::dispatch($media->url);
        }
    }
}
