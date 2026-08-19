<?php

namespace App\Filament\Resources\Flats\RelationManagers;

use App\Models\OwnershipTransfer;
use App\Models\OwnershipTransferItem;
use App\Models\User;
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
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class OwnersRelationManager extends RelationManager
{
    protected static string $relationship = 'owners';

    protected static ?string $title = 'ফ্ল্যাট মালিকের তথ্য';

    protected static bool $isLazy = false;

    public static function getModelLabel(): string
    {
        return ('ফ্ল্যাট মালিক');
    }

    public static function getPluralModelLabel(): string
    {
        return ('ফ্ল্যাট মালিকগণ');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                ->label('মালিক')
                ->options(
                    User::query()
                        ->orderBy('name')
                        ->pluck('name', 'id')
                )
                ->searchable()
                ->preload()
                ->required()

                // নতুন User তৈরি
                ->createOptionForm([

                    Grid::make(3)
                        ->schema([

                            TextInput::make('name')
                                ->label('নাম')
                                ->required(),

                            TextInput::make('email')
                                ->label('ইমেইল')
                                ->email()
                                ->required()
                                ->unique(
                                    table: 'users',
                                    column: 'email'
                                ),

                            TextInput::make('mobile')
                                ->label('মোবাইল')
                                ->required()
                                ->unique(
                                    table: 'users',
                                    column: 'mobile'
                                ),
                        ]),
                ])

                ->createOptionUsing(function (array $data): int {

                    $user = User::create([
                        'name'      => $data['name'],
                        'email'     => $data['email'],
                        'mobile'    => $data['mobile'],
                        'password'  => bcrypt('12345678'),
                        'is_active' => true,
                    ]);

                    return $user->id;
                }),

                TextInput::make('ownership_percent')
                    ->label(__('formlabel.ownership_percent'))
                    ->numeric()
                    ->minValue(0.01)
                    ->maxValue(100)
                    ->default(100)
                    ->required()
                    ->rules([
                        fn () => function (string $attribute, $value, \Closure $fail) {

                            $currentTotal = $this->getOwnerRecord()
                                ->owners()
                                ->where('is_current', true)
                                ->sum('ownership_percent');

                            $newTotal = (float) $currentTotal + (float) $value;

                            if ($newTotal > 100) {
                                $remaining = 100 - $currentTotal;

                                $fail(
                                    "বর্তমান মালিকদের মোট মালিকানা {$currentTotal}%। "
                                    . "সর্বোচ্চ আরও {$remaining}% মালিকানা যোগ করা যাবে।"
                                );
                            }
                        },
                    ]),

                DatePicker::make('start_date')
                    ->label('মালিকানা শুরুর তারিখ')
                    ->default(now())
                    ->required(),

                DatePicker::make('end_date')
                    ->label('মালিকানা শেষের তারিখ')
                    ->nullable(),

                Toggle::make('is_current')
                    ->label('বর্তমান মালিক')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('user')
            ->columns([
                TextColumn::make('user.name')
                    ->label(__('formlabel.owner_name'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('ownership_percent')
                    ->label(__('formlabel.ownership_percent'))
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_current')
                    ->label(__('formlabel.is_current'))
                    ->boolean(),
                TextColumn::make('createdBy.name')
                    ->label(__('formlabel.created_by'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('formlabel.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('formlabel.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus')
                    ->hidden(function () {

                        $currentTotal = $this->getOwnerRecord()
                            ->owners()
                            ->where('is_current', true)
                            ->sum('ownership_percent');

                        return $currentTotal >= 100;
                    })
                    ->using(function (array $data) {

                        return $this->getOwnerRecord()
                            ->owners()
                            ->create([
                                'user_id'           => $data['user_id'],
                                'ownership_percent' => $data['ownership_percent'],
                                'start_date'        => $data['start_date'],
                                'end_date'          => $data['end_date'] ?? null,
                                'is_current'        => $data['is_current'] ?? true,
                                'created_by'        => auth()->id(),
                            ]);
                    }),
                // AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make()
                    ->using(function ($record, array $data) {
                        $record->user->update([
                            'name' => $data['name'],
                            'email' => $data['email'],
                        ]);

                        $record->update([
                            'ownership_percent' => $data['ownership_percent'],
                            'is_current' => $data['is_current'],
                        ]);
                    }),
                    Action::make('transfer')
                        ->label('হস্তান্তর')
                        ->icon('heroicon-m-arrow-right')
                        ->color('warning')

                        ->visible(fn ($record) => $record->is_current)

                        ->schema([

                            Grid::make(3)
                                ->schema([

                                    Select::make('to_user_id')
                                        ->label('নতুন মালিক')
                                        ->options(function ($record) {

                                            return User::query()
                                                ->whereKeyNot($record->user_id)
                                                ->orderBy('name')
                                                ->pluck('name', 'id');
                                        })
                                        ->searchable()
                                        ->preload()
                                        ->required()

                                        ->createOptionForm([

                                            Grid::make(3)
                                                ->schema([

                                                    TextInput::make('name')
                                                        ->label('নাম')
                                                        ->required(),

                                                    TextInput::make('email')
                                                        ->label('ইমেইল')
                                                        ->email()
                                                        ->required(),

                                                    TextInput::make('mobile')
                                                        ->label('মোবাইল')
                                                        ->required()
                                                        ->rules([
                                                            \Illuminate\Validation\Rule::unique('users', 'mobile'),
                                                        ]),
                                                ]),
                                        ])

                                        ->createOptionUsing(function (array $data): int {

                                            $user = User::create([
                                                'name'      => $data['name'],
                                                'email'     => $data['email'],
                                                'mobile'    => $data['mobile'],
                                                'password'  => bcrypt('12345678'),
                                                'is_active' => true,
                                            ]);

                                            return $user->id;
                                        }),

                                    TextInput::make('ownership_percent')
                                        ->label('হস্তান্তরের পরিমাণ (%)')
                                        ->numeric()
                                        ->minValue(0.01)
                                        ->maxValue(fn ($record) => $record->ownership_percent)
                                        ->required(),

                                    Select::make('transfer_type')
                                        ->label('হস্তান্তরের ধরন')
                                        ->options([
                                            'purchase'    => 'ক্রয়',
                                            'inheritance' => 'উত্তরাধিকার',
                                            'gift'        => 'দান',
                                            'transfer'    => 'হস্তান্তর',
                                            'other'       => 'অন্যান্য',
                                        ])
                                        ->default('transfer')
                                        ->required(),
                                ]),

                            Grid::make(3)
                                ->schema([

                                    DatePicker::make('transfer_date')
                                        ->label('হস্তান্তরের তারিখ')
                                        ->default(now())
                                        ->required(),

                                    TextInput::make('document_no')
                                        ->label('দলিল নম্বর'),

                                    FileUpload::make('document_file')
                                        ->label('দলিল')
                                        ->disk('public')
                                        ->directory('ownership-transfers'),
                                ]),

                            Textarea::make('remarks')
                                ->label('মন্তব্য')
                                ->columnSpanFull(),
                        ])

                        ->modalHeading( fn ($record) => "ফ্ল্যাটের মালিকানা হস্তান্তর —> {$record->flat->floor->building->plot->plot_no} 
                                        —> {$record->flat->floor->building->building_name} —> {$record->flat->floor->floor_name} 
                                        —> {$record->flat->flat_no}")
                        ->modalSubmitActionLabel('হস্তান্তর করুন')
                        ->modalCancelActionLabel('বাতিল')

                        ->action(function ($record, array $data) {

                            DB::transaction(function () use ($record, $data) {

                                $flat = $this->getOwnerRecord();

                                $transferPercent = (float) $data['ownership_percent'];
                                $oldPercent      = (float) $record->ownership_percent;

                                /*
                                |--------------------------------------------------------------------------
                                | ১. Ownership Transfer তৈরি
                                |--------------------------------------------------------------------------
                                */

                                $transfer = OwnershipTransfer::create([

                                    'ownable_type' => $flat->getMorphClass(),

                                    'ownable_id'   => $flat->id,

                                    'transfer_type' =>
                                        $data['transfer_type'],

                                    'transfer_date' =>
                                        $data['transfer_date'],

                                    'document_no' =>
                                        $data['document_no'] ?? null,

                                    'document_file' =>
                                        $data['document_file'] ?? null,

                                    'remarks' =>
                                        $data['remarks'] ?? null,

                                    'created_by' =>
                                        auth()->id(),
                                ]);

                                /*
                                |--------------------------------------------------------------------------
                                | ২. FROM Owner Transfer Item
                                |--------------------------------------------------------------------------
                                */

                                OwnershipTransferItem::create([

                                    'ownership_transfer_id' =>
                                        $transfer->id,

                                    'owner_type' =>
                                        $record->getMorphClass(),

                                    'owner_id' =>
                                        $record->id,

                                    'direction' =>
                                        'from',

                                    'ownership_percent' =>
                                        $transferPercent,
                                ]);

                                /*
                                |--------------------------------------------------------------------------
                                | ৩. পুরনো মালিকের মালিকানা কমানো
                                |--------------------------------------------------------------------------
                                */

                                $remainingPercent =
                                    $oldPercent - $transferPercent;

                                if ($remainingPercent <= 0) {

                                    $record->update([

                                        'ownership_percent' => 0,

                                        'is_current' => false,

                                        'end_date' =>
                                            $data['transfer_date'],
                                    ]);

                                } else {

                                    $record->update([

                                        'ownership_percent' =>
                                            $remainingPercent,
                                    ]);
                                }

                                /*
                                |--------------------------------------------------------------------------
                                | ৪. নতুন মালিক তৈরি / আগে থাকলে ব্যবহার
                                |--------------------------------------------------------------------------
                                */

                                $newOwner = $flat->owners()
                                    ->where('user_id', $data['to_user_id'])
                                    ->first();

                                if ($newOwner) {

                                    $newOwner->update([

                                        'ownership_percent' =>
                                            $newOwner->ownership_percent
                                            + $transferPercent,

                                        'is_current' =>
                                            true,

                                        'end_date' =>
                                            null,
                                    ]);

                                } else {

                                    $newOwner = $flat->owners()->create([

                                        'user_id' =>
                                            $data['to_user_id'],

                                        'ownership_percent' =>
                                            $transferPercent,

                                        'start_date' =>
                                            $data['transfer_date'],

                                        'end_date' =>
                                            null,

                                        'is_current' =>
                                            true,

                                        'created_by' =>
                                            auth()->id(),
                                    ]);
                                }

                                /*
                                |--------------------------------------------------------------------------
                                | ৫. TO Owner Transfer Item
                                |--------------------------------------------------------------------------
                                */

                                OwnershipTransferItem::create([

                                    'ownership_transfer_id' =>
                                        $transfer->id,

                                    'owner_type' =>
                                        $newOwner->getMorphClass(),

                                    'owner_id' =>
                                        $newOwner->id,

                                    'direction' =>
                                        'to',

                                    'ownership_percent' =>
                                        $transferPercent,
                                ]);
                            });
                        }),

                    DeleteAction::make(),
                // DissociateAction::make(),
                //  DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // DissociateBulkAction::make(),
                    // DeleteBulkAction::make(),
                ]),
            ]);
    }
}
