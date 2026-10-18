<?php

declare(strict_types=1);

namespace App\Observers;

use App\Jobs\SyncMediaFile;
use App\Models\Category;
use App\Models\Tag;

final class LabelIconObserver
{
    public function saved(Category|Tag $label): void
    {
        if (! app()->environment(['local', 'testing']) && $label->isDirty('icon')
            && is_string($label->icon) && str_starts_with($label->icon, 'uploads/')) {
            SyncMediaFile::dispatch($label->icon);
        }
    }
}
