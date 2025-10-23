<?php

declare(strict_types=1);

namespace App\Filament\Resources\Tags\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class TagsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('icon')
                    ->searchable(),
                TextColumn::make('activities_count')->counts('activities'),
                TextColumn::make('courses_count')->counts('courses'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()->label(''),
                EditAction::make()->label(''),
                Action::make('delete')
                    ->color('danger')
                    ->label('')
                    ->icon(Heroicon::Trash)
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->delete();
                        Notification::make()
                            ->title('Tag delete')
                            ->danger()
                            ->send();
                    })
                    ->hidden(function ($record) {
                        return $record->activities()->count() > 0 || $record->courses()->count() > 0;
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                ]),
            ]);
    }
}
