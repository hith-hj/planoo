<?php

namespace App\Filament\Resources\Appointments\Tables;

use App\Enums\AppointmentStatus;
use App\Enums\SessionDuration;
use App\Filament\Resources\Activities\ActivityResource;
use App\Filament\Resources\Courses\CourseResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AppointmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function(Builder $query){
                return $query->with(['customer', 'holder']);
            })
            ->columns([
                TextColumn::make('appointable')
                    ->state(fn($record) => class_basename($record->appointable_type).":".$record->holder->name)
                    ->url(function ($record) {
                        $class = strtolower(class_basename($record->appointable_type));
                        $model = $record->holder;
                        return match ($class) {
                            'activity' => ActivityResource::getUrl('view', ['record' => $model->id]),
                            'course' => CourseResource::getUrl('view', ['record' => $model->id]),
                            default => '#',
                        };
                    })
                    ->openUrlInNewTab(),
                TextColumn::make('customer.name')
                    ->searchable(),
                TextColumn::make('date')
                    ->date()
                    ->sortable(),
                TextColumn::make('time')
                    ->time()
                    ->sortable(),
                TextColumn::make('price')
                    ->money('syp')
                    ->sortable(),
                TextColumn::make('session_duration')
                    ->formatStateUsing(fn($state) => SessionDuration::from($state)->name)
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn($state) => AppointmentStatus::from($state)->name)
                    ->sortable(),
                TextColumn::make('notes')
                    ->limit(20)
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
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
                    // DeleteBulkAction::make(),
                ]),
            ]);
    }
}
