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
use Filament\Schemas\Components\Utilities\Set;
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
        // বর্তমান Building-এর total_floor নেওয়া
        $totalFloor = $this->getOwnerRecord()->total_floor ?? 0;

        // 0 থেকে total_floor পর্যন্ত option তৈরি
        $floorOptions = [];

        for ($i = 0; $i <= $totalFloor; $i++) {
            $floorOptions[$i] = $i == 0
                ? '০'
                : $this->banglaNumber($i);
        }
        return $schema
            ->components([
                Select::make('floor_no')
                ->label(__('formlabel.floor_no'))
                ->options($floorOptions)
                ->required()
                ->live()
                ->afterStateUpdated(function ($state, Set $set) {
                    if ($state === null || $state === '') {
                        $set('floor_name', null);
                        return;
                    }

                    $floorNo = (int) $state;

                    if ($floorNo === 0) {
                        $set('floor_name', 'গ্রাউন্ড ফ্লোর');
                    } else {
                        $set(
                            'floor_name',
                            $this->getBanglaFloorName($floorNo)
                        );
                    }
                }),
                TextInput::make('floor_name')
                    ->label(__('formlabel.floor_name'))
                    ->nullable()
                    ->disabled()
                    ->dehydrated(),
                Section::make('ফ্ল্যাটসমূহের বিবরণ')
                    ->schema([

                    Repeater::make('ফ্ল্যাট')
                        ->relationship('flats')
                        ->label('ফ্ল্যাটসমূহ')
                        ->addActionLabel('নতুন ফ্ল্যাট যোগ করুন')
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
                                    'none' => 'কোনটি নয়',
                                ]),
                            TextInput::make('flat_area')
                                ->label(__('formlabel.flat_area'))
                                ->numeric()
                                ->nullable()
                                ->suffix('বর্গফুট'),
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

    protected function banglaNumber(int $number): string
    {
        $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $bangla  = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];

        return str_replace($english, $bangla, (string) $number);
    }


    protected function getBanglaFloorName(int $floorNo): string
    {
        $banglaNumber = $this->banglaNumber($floorNo);

        return match ($floorNo) {
            0  => 'গ্রাউন্ড ফ্লোর',
            1  => "{$banglaNumber}ম তলা",
            2  => "{$banglaNumber}য় তলা",
            3  => "{$banglaNumber}য় তলা",
            4  => "{$banglaNumber}র্থ তলা",
            5  => "{$banglaNumber}ম তলা",
            6  => "{$banglaNumber}ষ্ঠ তলা",
            7  => "{$banglaNumber}ম তলা",
            8  => "{$banglaNumber}ম তলা",
            9  => "{$banglaNumber}ম তলা",
            10 => "{$banglaNumber}ম তলা",
            11 => "{$banglaNumber}তম তলা",
            12 => "{$banglaNumber}তম তলা",
            13 => "{$banglaNumber}তম তলা",
            14 => "{$banglaNumber}তম তলা",
            15 => "{$banglaNumber}তম তলা",
            16 => "{$banglaNumber}তম তলা",
            17 => "{$banglaNumber}তম তলা",
            18 => "{$banglaNumber}তম তলা",
            19 => "{$banglaNumber}তম তলা",
            20 => "{$banglaNumber}তম তলা",
            default => "{$banglaNumber}তম তলা",
        };
    }
}
