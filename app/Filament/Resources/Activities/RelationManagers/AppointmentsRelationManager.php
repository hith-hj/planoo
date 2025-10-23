<?php

declare(strict_types=1);

namespace App\Filament\Resources\Activities\RelationManagers;

use App\Filament\Resources\Appointments\AppointmentResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

final class AppointmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'appointments';

    protected static ?string $relatedResource = AppointmentResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([]);
    }
}
