<?php

namespace App\Filament\Resources\Flats\RelationManagers;

use App\Models\Occupancy;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OccupanciesRelationManager extends RelationManager
{
    protected static string $relationship = 'occupancies';
    protected static bool $isLazy = false;

    protected static ?string $title = 'বসবাসকারিদের সামারি';

    public static function getModelLabel(): string
    {
        return ('সামারি');
    }

    public static function getPluralModelLabel(): string
    {
        return ('সামারিসমূহ');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('occupancy_type')
                    ->options(['owner' => 'Owner', 'tenant' => 'Tenant', 'vacant' => 'Vacant'])
                    ->required(),
                DatePicker::make('start_date')
                    ->required(),
                DatePicker::make('end_date'),
                Toggle::make('is_current')
                    ->required(),
            ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('occupancy_type')
                    ->badge(),
                TextEntry::make('start_date')
                    ->date(),
                TextEntry::make('end_date')
                    ->date()
                    ->placeholder('-'),
                IconEntry::make('is_current')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('occupancy_type')
            ->columns([
                    TextColumn::make('occupancy_type')
                    ->label('দখল অবস্থা')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'owner'  => 'মালিক বসবাস',
                        'tenant' => 'ভাড়াটিয়া',
                        'vacant' => 'খালি',
                        default  => $state,
                    }),

                TextColumn::make('rentalAgreement.tenant.tenant_name')
                    ->label('ভাড়াটিয়ার নাম')
                    ->searchable(),

                TextColumn::make('rentalAgreement.tenant.mobile')
                    ->label('মোবাইল'),

                TextColumn::make('family_members_count')
                    ->label('পরিবারের সদস্য')
                    ->state(function (Occupancy $record) {
                        return $record->rentalAgreement?->familyMembers()->count() ?? 0;
                    }),

                TextColumn::make('start_date')
                    ->label('শুরুর তারিখ')
                    ->date(),

                TextColumn::make('end_date')
                    ->label('শেষের তারিখ')
                    ->date(),

                IconColumn::make('is_current')
                    ->label('বর্তমান')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // CreateAction::make(),
                // AssociateAction::make(),
            ])
            ->recordActions([
                // ViewAction::make(),
                // EditAction::make(),
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
