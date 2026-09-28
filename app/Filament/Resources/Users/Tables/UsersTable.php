<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

final class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('account_type')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('phone')
                    ->searchable(),
                TextColumn::make('country_code')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('rate')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('status')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                IconColumn::make('is_active')
                    ->boolean(),
                TextColumn::make('verified_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()->label(''),
                DeleteAction::make()
                    ->label('')
                    ->requiresConfirmation()
                    ->modalDescription('This is a super dangerous action, it is going to delete the user and all his related resources'),
                Action::make('activation')
                    ->requiresConfirmation()
                    ->label(fn (Model $record) => $record->is_active ? 'deactivate' : 'activate')
                    ->action(fn (Model $record) => $record->update(['is_active' => ! $record->is_active])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([]),
            ]);
    }
}
