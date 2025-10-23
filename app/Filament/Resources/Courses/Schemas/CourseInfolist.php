<?php

declare(strict_types=1);

namespace App\Filament\Resources\Courses\Schemas;

use App\Enums\SessionDuration;
use App\Filament\Resources\Users\UserResource;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

final class CourseInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user.name')
                    ->url(fn ($record) => UserResource::getUrl('view', ['record' => $record->user]))
                    ->openUrlInNewTab(),
                TextEntry::make('category.name')
                    ->label('Category'),
                TextEntry::make('name'),
                IconEntry::make('is_active')
                    ->boolean(),
                IconEntry::make('is_full')
                    ->boolean(),
                TextEntry::make('price')
                    ->money('syp'),
                TextEntry::make('session_duration')
                    ->formatStateUsing(fn ($state) => SessionDuration::from($state)->name),
                TextEntry::make('course_duration')
                    ->numeric()
                    ->suffix('days'),
                TextEntry::make('capacity')
                    ->numeric(),
                TextEntry::make('rate')
                    ->numeric(),
                TextEntry::make('cancellation_fee')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('description')
                    ->columnSpanFull(),
            ])->columns(4);
    }
}
