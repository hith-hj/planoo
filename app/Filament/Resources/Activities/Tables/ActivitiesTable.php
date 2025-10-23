<?php

declare(strict_types=1);

namespace App\Filament\Resources\Activities\Tables;

use App\Enums\SessionDuration;
use App\Filament\Resources\Users\UserResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

final class ActivitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                return $query->with(['user', 'category', 'appointments']);
            })
            ->columns([
                TextColumn::make('user.name')
                    ->searchable()
                    ->url(fn ($record) => UserResource::getUrl('view', ['record' => $record->user]))
                    ->openUrlInNewTab(),
                TextColumn::make('category.name')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('session_duration')
                    ->formatStateUsing(fn ($state) => SessionDuration::from($state)->name)
                    ->sortable(),
                TextColumn::make('price')
                    ->money('syp')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->boolean(),
                TextColumn::make('appointments_count')->counts('appointments'),
            ])
            ->columnManager(false)
            ->filters([
                SelectFilter::make('is_active')
                    ->options(['Inactive', 'Active']),
            ])
            ->recordActions([
                ViewAction::make()->label(''),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // DeleteBulkAction::make(),
                ]),
            ]);
    }
}
