<?php

declare(strict_types=1);

namespace App\Filament\Resources\Activities\Pages;

use App\Filament\Resources\Activities\ActivityResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateActivity extends CreateRecord
{
    protected static string $resource = ActivityResource::class;
}
