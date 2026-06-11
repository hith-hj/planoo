<?php

declare(strict_types=1);

namespace App\Filament\Resources\Tags\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rule;

final class TagForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                FileUpload::make('icon')
                    ->required()
                    ->label('Upload Category Icon')
                    ->acceptedFileTypes(['image/png'])
                    ->imageEditor()
                    ->maxSize(100)
                    ->rule(Rule::dimensions()->maxWidth(50)->maxHeight(50))
                    ->disk('public')
                    ->directory('uploads/tags_icons'),
            ])
            ->columns(1);
    }
}
