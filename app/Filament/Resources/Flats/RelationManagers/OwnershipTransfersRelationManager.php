<?php

namespace App\Filament\Resources\Flats\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OwnershipTransfersRelationManager extends RelationManager
{
    protected static string $relationship = 'ownershipTransfers';

    protected static ?string $title = 'মালিকানা হস্তান্তরের ইতিহাস';

    protected static bool $isLazy = false;

    public static function getModelLabel(): string
    {
        return 'মালিকানা হস্তান্তর';
    }

    public static function getPluralModelLabel(): string
    {
        return 'মালিকানা হস্তান্তরের ইতিহাস';
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')

            ->columns([

                TextColumn::make('transfer_date')
                    ->label('তারিখ')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('transfer_type')
                    ->label('হস্তান্তরের ধরন')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'purchase'    => 'ক্রয়',
                        'inheritance' => 'উত্তরাধিকার',
                        'gift'        => 'দান',
                        'transfer'    => 'হস্তান্তর',
                        'other'       => 'অন্যান্য',
                        default       => $state,
                    })
                    ->badge(),

                TextColumn::make('from_owner')
                    ->label('যার কাছ থেকে')
                    ->state(function ($record) {
                        return $record->items
                            ->where('direction', 'from')
                            ->map(fn ($item) =>
                                $item->owner?->user?->name
                            )
                            ->filter()
                            ->implode(', ');
                    }),

                TextColumn::make('to_owner')
                    ->label('যার কাছে')
                    ->state(function ($record) {
                        return $record->items
                            ->where('direction', 'to')
                            ->map(fn ($item) =>
                                $item->owner?->user?->name
                            )
                            ->filter()
                            ->implode(', ');
                    }),

                TextColumn::make('ownership_percent')
                    ->label('হস্তান্তরের পরিমাণ')
                    ->state(function ($record) {

                        return $record->items
                            ->where('direction', 'from')
                            ->sum('ownership_percent') . '%';
                    }),

                TextColumn::make('document_no')
                    ->label('দলিল নম্বর')
                    ->placeholder('-'),

                TextColumn::make('createdBy.name')
                    ->label('তৈরি করেছেন'),

            ])

            ->recordActions([

                Action::make('view')
                    ->label('বিস্তারিত')
                    ->icon('heroicon-o-eye')
                    ->modalHeading(
                        fn ($record) =>
                            'মালিকানা হস্তান্তরের বিস্তারিত'
                    )
                    ->modalContent(
                        fn ($record) =>
                            view(
                                'filament.ownership-transfer-details',
                                [
                                    'transfer' => $record,
                                ]
                            )
                    )
                    ->modalSubmitAction(false),

            ])

            ->toolbarActions([
                BulkActionGroup::make([]),
            ]);
    }
}
