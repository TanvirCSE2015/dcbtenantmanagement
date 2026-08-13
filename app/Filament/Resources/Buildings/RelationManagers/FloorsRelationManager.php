<?php

namespace App\Filament\Resources\Buildings\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FloorsRelationManager extends RelationManager
{
    protected static string $relationship = 'floors';

    protected static ?string $title = 'ফ্লোরসমূহের তথ্য';

    protected static bool $isLazy = false;

    public static function getModelLabel(): string
    {
        return ('ফ্লোর');
    }

    public static function getPluralModelLabel(): string
    {
        return ('ফ্লোরসমূহ');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('floor_no')
                    ->label(__('formlabel.floor_no'))
                    ->required()
                    ->numeric(),
                TextInput::make('floor_name')
                    ->label(__('formlabel.floor_name'))
                    ->nullable(),
                Section::make('ফ্ল্যাটসমূহের বিবরণ')
                    ->schema([

                    Repeater::make('ফ্ল্যাট')
                        ->relationship('flats')
                        ->schema([
                            TextInput::make('flat_no')
                                ->label(__('formlabel.flat_no'))
                                ->required(),
                            Select::make('flat_side')
                                ->label(__('formlabel.flat_side'))
                                ->nullable()
                                ->options([
                                    'North' => 'উত্তর',
                                    'South' => 'দক্ষিণ',
                                    'East' => 'পূর্ব',
                                    'West' => 'পশ্চিম',
                                ]),
                            TextInput::make('flat_area')
                                ->label(__('formlabel.flat_area'))
                                ->numeric()
                                ->nullable(),
                        ])
                        ->columns(3)

                    ])->columnSpanFull(),       
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('floor_no')
            ->columns([
                TextColumn::make('floor_no')
                    ->label(__('formlabel.floor_no'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('floor_name')
                    ->label(__('formlabel.floor_name'))
                    ->sortable(),
                TextColumn::make('flats_count')
                    ->label(__('formlabel.flats'))
                    ->counts('flats')
                    ->sortable(),
                TextColumn::make('flats.flat_no')
                    ->label(__('formlabel.flat_no')),
                // TextColumn::make('created_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                // TextColumn::make('updated_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                  ->icon('heroicon-o-plus'),
                // AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                // DissociateAction::make(),
                // DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // DissociateBulkAction::make(),
                    // DeleteBulkAction::make(),
                ]),
            ]);
    }
}
