<?php

declare(strict_types=1);

namespace App\Filament\Resources\Tags\Pages;

use App\Filament\Resources\Tags\TagResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Artisan;

final class CreateTag extends CreateRecord
{
    protected static string $resource = TagResource::class;

    protected function afterCreate(): void
    {
        defer(fn () => Artisan::call('app:sync-files-to-public'));
    }
}
