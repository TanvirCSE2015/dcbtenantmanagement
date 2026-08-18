<?php

namespace App\Filament\Resources\Areas\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AreaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('formlabel.area_info'))
                    ->schema([
                        TextInput::make('area_name')
                    ->label(__('formlabel.area_name'))
                    ->required(),
                    Textarea::make('description')
                        ->label(__('formlabel.description'))
                        ->default(null)
                        ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
