<?php

namespace App\Filament\Resources\Courses\Tables;

use App\Enums\CourseDuration;
use App\Enums\SessionDuration;
use App\Filament\Resources\Users\UserResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CoursesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->url(fn($record) => UserResource::getUrl('view', ['record' => $record->user]))
                    ->openUrlInNewTab()
                    ->searchable(),
                TextColumn::make('category.name')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                IconColumn::make('is_active')
                    ->boolean(),
                IconColumn::make('is_full')
                    ->boolean(),
                TextColumn::make('price')
                    ->money('syp')
                    ->sortable(),
                TextColumn::make('session_duration')
                    ->formatStateUsing(fn($state) => SessionDuration::from($state)->name)
                    ->sortable(),
                TextColumn::make('course_duration')
                    ->formatStateUsing(fn($state) => CourseDuration::from($state)->name)
                    ->sortable(),
                TextColumn::make('capacity')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('rate')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('cancellation_fee')
                    ->numeric()
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
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                ]),
            ]);
    }
}
