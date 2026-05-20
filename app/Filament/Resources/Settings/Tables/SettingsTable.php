<?php

declare(strict_types=1);

namespace App\Filament\Resources\Settings\Tables;

use App\Enums\AdminsRoles;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

final class SettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('value'),
                TextColumn::make('description'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()->hidden(fn () => Auth::user()->role !== AdminsRoles::super->value),
            ])
            ->toolbarActions([
                BulkActionGroup::make([]),
            ]);
    }
}
