<?php

declare(strict_types=1);

namespace App\Filament\Resources\Customers\Tables;

use App\Enums\AccountStatus;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

final class CustomersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('phone')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => AccountStatus::from($state)->name)
                    ->sortable(),
                IconColumn::make('is_active')
                    ->boolean()
                    ->toggleable(),
                TextColumn::make('verified_by')
                    ->searchable(),
                TextColumn::make('verified_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->columnManager(false)
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()->label(''),
                Action::make('toggle')
                    ->requiresConfirmation()
                    ->action(function (Model $record) {
                        $record->update(['is_active' => ! $record->is_active]);
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // DeleteBulkAction::make(),
                ]),
            ]);
    }
}
