<?php

declare(strict_types=1);

namespace App\Filament\Resources\Appointments\Schemas;

use App\Enums\AppointmentStatus;
use App\Enums\SessionDuration;
use App\Filament\Resources\Activities\ActivityResource;
use App\Filament\Resources\Courses\CourseResource;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

final class AppointmentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('appointable')
                    ->state(fn ($record) => class_basename($record->appointable_type).':'.$record->holder->name)
                    ->url(function ($record) {
                        $class = mb_strtolower(class_basename($record->appointable_type));
                        $model = $record->holder;

                        return match ($class) {
                            'activity' => ActivityResource::getUrl('view', ['record' => $model->id]),
                            'course' => CourseResource::getUrl('view', ['record' => $model->id]),
                            default => '#',
                        };
                    })
                    ->openUrlInNewTab(),
                TextEntry::make('customer.name')
                    ->label('Customer')
                    ->placeholder('-'),
                TextEntry::make('date')
                    ->date(),
                TextEntry::make('time')
                    ->time(),
                TextEntry::make('price')
                    ->money('syp'),
                TextEntry::make('session_duration')
                    ->formatStateUsing(fn ($state) => SessionDuration::from($state)->name),
                TextEntry::make('status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => AppointmentStatus::from($state)->name),
                TextEntry::make('canceled_by')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('notes')
                    ->placeholder('-'),
            ])
            ->columns(4);
    }
}
