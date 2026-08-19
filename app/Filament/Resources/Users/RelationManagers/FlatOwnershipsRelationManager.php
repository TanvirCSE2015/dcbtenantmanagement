<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Models\FlatOwner;
use App\Models\OwnershipTransfer;
use App\Models\OwnershipTransferItem;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
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
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class FlatOwnershipsRelationManager extends RelationManager
{
    protected static string $relationship = 'flatOwnerships';

    protected static ?string $title = 'ফ্ল্যাট';

    protected static bool $isLazy = false;

    public static function getModelLabel(): string
    {
        return ('ফ্ল্যাট');
    }

    public static function getPluralModelLabel(): string
    {
        return ('ফ্ল্যাটসমূহ');
    }

     public function form(Schema $schema): Schema
    {
        return $schema->components([

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

                ->createOptionForm([
                    Grid::make(3)->schema([

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

                ->maxValue(function () {

                    $currentTotal = $this->getOwnerRecord()
                        ->flatOwnerships()
                        ->where('is_current', true)
                        ->sum('ownership_percent');

                    return max(0, 100 - $currentTotal);
                })

                ->default(function () {

                    $currentTotal = $this->getOwnerRecord()
                        ->flatOwnerships()
                        ->where('is_current', true)
                        ->sum('ownership_percent');

                    return max(0, 100 - $currentTotal);
                })

                ->required()

                ->rules([
                    fn () => function (
                        string $attribute,
                        $value,
                        \Closure $fail
                    ) {

                        $currentTotal = $this->getOwnerRecord()
                            ->flatOwnerships()
                            ->where('is_current', true)
                            ->sum('ownership_percent');

                        $newTotal =
                            (float) $currentTotal +
                            (float) $value;

                        if ($newTotal > 100) {

                            $remaining =
                                max(0, 100 - $currentTotal);

                            $fail(
                                "বর্তমান মালিকদের মোট মালিকানা {$currentTotal}%। "
                                . "সর্বোচ্চ আরও {$remaining}% যোগ করা যাবে।"
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
                    ->label('নাম')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('ownership_percent')
                    ->label('মালিকানা %')
                    ->numeric(
                        decimalPlaces: 2
                    )
                    ->sortable(),

                IconColumn::make('is_current')
                    ->label('বর্তমান')
                    ->boolean(),

                TextColumn::make('start_date')
                    ->label('শুরুর তারিখ')
                    ->date()
                    ->sortable(),

                TextColumn::make('end_date')
                    ->label('শেষের তারিখ')
                    ->date()
                    ->placeholder('-')
                    ->sortable(),

                TextColumn::make('createdBy.name')
                    ->label('তৈরি করেছেন')
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

            ])

            ->headerActions([

                CreateAction::make()
                    ->label('মালিক যোগ করুন')
                    ->icon('heroicon-o-plus')

                    ->hidden(function () {

                        $currentTotal = $this->getOwnerRecord()
                            ->flatOwnerships()
                            ->where('is_current', true)
                            ->sum('ownership_percent');

                        return $currentTotal >= 100;
                    })

                    ->using(function (array $data) {

                        return $this->getOwnerRecord()
                            ->flatOwnerships()
                            ->create([

                                'user_id' =>
                                    $data['user_id'],

                                'ownership_percent' =>
                                    $data['ownership_percent'],

                                'start_date' =>
                                    $data['start_date'],

                                'end_date' =>
                                    $data['end_date'] ?? null,

                                'is_current' =>
                                    $data['is_current'] ?? true,

                                'created_by' =>
                                    auth()->id(),
                            ]);
                    }),
            ])

            ->recordActions([

                /*
                |--------------------------------------------------------------------------
                | EDIT
                |--------------------------------------------------------------------------
                */

                EditAction::make()

                    ->using(function ($record, array $data) {

                        $record->user->update([

                            'name' =>
                                $data['name'],

                            'email' =>
                                $data['email'],

                            'mobile' =>
                                $data['mobile'],
                        ]);

                        $record->update([

                            'ownership_percent' =>
                                $data['ownership_percent'],

                            'start_date' =>
                                $data['start_date'],

                            'end_date' =>
                                $data['end_date'] ?? null,

                            'is_current' =>
                                $data['is_current'] ?? true,
                        ]);

                        return $record;
                    }),

                /*
                |--------------------------------------------------------------------------
                | TRANSFER
                |--------------------------------------------------------------------------
                */

                Action::make('transfer')

                    ->label('হস্তান্তর')

                    ->icon('heroicon-m-arrow-right')

                    ->color('warning')

                    ->visible(
                        fn ($record) =>
                            $record->is_current
                    )

                    ->schema([

                        Grid::make(3)->schema([

                            /*
                            |--------------------------------------------------------------------------
                            | নতুন মালিক
                            |--------------------------------------------------------------------------
                            */

                            Select::make('to_user_id')

                                ->label('নতুন মালিক')

                                ->options(function ($record) {

                                    return User::query()

                                        ->where(
                                            'id',
                                            '!=',
                                            $record->user_id
                                        )

                                        ->orderBy('name')

                                        ->pluck(
                                            'name',
                                            'id'
                                        );
                                })

                                ->searchable()

                                ->preload()

                                ->required()

                                ->createOptionForm([

                                    Grid::make(3)->schema([

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
                                                Rule::unique(
                                                    'users',
                                                    'mobile'
                                                ),
                                            ]),
                                    ]),
                                ])

                                ->createOptionUsing(
                                    function (
                                        array $data
                                    ): int {

                                        $user = User::create([

                                            'name' =>
                                                $data['name'],

                                            'email' =>
                                                $data['email'],

                                            'mobile' =>
                                                $data['mobile'],

                                            'password' =>
                                                bcrypt(
                                                    '12345678'
                                                ),

                                            'is_active' =>
                                                true,
                                        ]);

                                        return $user->id;
                                    }
                                ),

                            /*
                            |--------------------------------------------------------------------------
                            | Transfer Percentage
                            |--------------------------------------------------------------------------
                            */

                            TextInput::make(
                                'ownership_percent'
                            )

                                ->label(
                                    'হস্তান্তরের পরিমাণ (%)'
                                )

                                ->numeric()

                                ->minValue(0.01)

                                ->maxValue(
                                    fn ($record) =>
                                        $record
                                            ->ownership_percent
                                )

                                ->rules([

                                    fn ($record) =>
                                        function (
                                            string $attribute,
                                            $value,
                                            \Closure $fail
                                        ) use ($record) {

                                            if (
                                                (float) $value >
                                                (float) $record
                                                    ->ownership_percent
                                            ) {

                                                $fail(
                                                    "হস্তান্তরের পরিমাণ "
                                                    . "{$record->ownership_percent}% "
                                                    . "এর বেশি হতে পারবে না।"
                                                );
                                            }
                                        },
                                ])

                                ->required(),

                            /*
                            |--------------------------------------------------------------------------
                            | Transfer Type
                            |--------------------------------------------------------------------------
                            */

                            Select::make(
                                'transfer_type'
                            )

                                ->label(
                                    'হস্তান্তরের ধরন'
                                )

                                ->options([

                                    'purchase' =>
                                        'ক্রয়',

                                    'inheritance' =>
                                        'উত্তরাধিকার',

                                    'gift' =>
                                        'দান',

                                    'transfer' =>
                                        'হস্তান্তর',

                                    'other' =>
                                        'অন্যান্য',
                                ])

                                ->default('transfer')

                                ->required(),
                        ]),

                        /*
                        |--------------------------------------------------------------------------
                        | Transfer Details
                        |--------------------------------------------------------------------------
                        */

                        Grid::make(3)->schema([

                            DatePicker::make(
                                'transfer_date'
                            )

                                ->label(
                                    'হস্তান্তরের তারিখ'
                                )

                                ->default(now())

                                ->required(),

                            TextInput::make(
                                'document_no'
                            )

                                ->label(
                                    'দলিল নম্বর'
                                ),

                            FileUpload::make(
                                'document_file'
                            )

                                ->label(
                                    'দলিল / PDF'
                                )

                                ->disk('public')

                                ->directory(
                                    'ownership-transfers'
                                )

                                ->acceptedFileTypes([

                                    'application/pdf',

                                    'image/jpeg',

                                    'image/png',
                                ])

                                ->maxSize(10240),
                        ]),

                        Textarea::make('remarks')
                            ->label('মন্তব্য')
                            ->columnSpanFull(),
                    ])

                    ->modalHeading(
                        fn ($record) =>
                            "ফ্ল্যাট মালিকানা হস্তান্তর — "
                            . "{$record->user?->name} — "
                            . "{$record->flat?->floor?->building?->plot?->plot_no} — "
                            . "{$record->flat?->flat_no} — "
                            . "{$record->ownership_percent}%"
                    )

                    ->modalSubmitActionLabel(
                        'হস্তান্তর করুন'
                    )

                    ->modalCancelActionLabel(
                        'বাতিল'
                    )

                    ->modalWidth('4xl')

                    ->action(
                        function (
                            $record,
                            array $data
                        ) {

                            DB::transaction(
                                function () use (
                                    $record,
                                    $data
                                ) {

                                    /*
                                    |--------------------------------------------------------------------------
                                    | ১. Current Owner Lock
                                    |--------------------------------------------------------------------------
                                    */

                                    $currentOwner =
                                        FlatOwner::query()

                                            ->whereKey(
                                                $record->id
                                            )

                                            ->lockForUpdate()

                                            ->firstOrFail();

                                    /*
                                    |--------------------------------------------------------------------------
                                    | ২. Active Check
                                    |--------------------------------------------------------------------------
                                    */

                                    if (
                                        ! $currentOwner
                                            ->is_current
                                    ) {

                                        throw ValidationException::withMessages([

                                            'ownership_percent' =>
                                                'এই মালিক বর্তমানে আর সক্রিয় মালিক নন।',
                                        ]);
                                    }

                                    /*
                                    |--------------------------------------------------------------------------
                                    | ৩. Percentage Validation
                                    |--------------------------------------------------------------------------
                                    */

                                    $transferPercent =
                                        (float)
                                        $data[
                                            'ownership_percent'
                                        ];

                                    $currentPercent =
                                        (float)
                                        $currentOwner
                                            ->ownership_percent;

                                    if (
                                        $transferPercent <= 0
                                    ) {

                                        throw ValidationException::withMessages([

                                            'ownership_percent' =>
                                                'হস্তান্তরের পরিমাণ ০ এর বেশি হতে হবে।',
                                        ]);
                                    }

                                    if (
                                        $transferPercent >
                                        $currentPercent
                                    ) {

                                        throw ValidationException::withMessages([

                                            'ownership_percent' =>
                                                "হস্তান্তরের পরিমাণ "
                                                . "{$currentPercent}% "
                                                . "এর বেশি হতে পারবে না।",
                                        ]);
                                    }

                                    /*
                                    |--------------------------------------------------------------------------
                                    | ৪. New User Check
                                    |--------------------------------------------------------------------------
                                    */

                                    $toUserId =
                                        (int)
                                        $data[
                                            'to_user_id'
                                        ];

                                    if (
                                        $toUserId ===
                                        (int)
                                        $currentOwner->user_id
                                    ) {

                                        throw ValidationException::withMessages([

                                            'to_user_id' =>
                                                'বর্তমান মালিককে নতুন মালিক হিসেবে নির্বাচন করা যাবে না।',
                                        ]);
                                    }

                                    /*
                                    |--------------------------------------------------------------------------
                                    | ৫. Flat
                                    |--------------------------------------------------------------------------
                                    */

                                    $flat =
                                        $this->getOwnerRecord();

                                    /*
                                    |--------------------------------------------------------------------------
                                    | ৬. Ownership Transfer Header
                                    |--------------------------------------------------------------------------
                                    */

                                    $transfer =
                                        OwnershipTransfer::create([

                                            'ownable_type' =>
                                                $flat::class,

                                            'ownable_id' =>
                                                $flat->id,

                                            'transfer_type' =>
                                                $data[
                                                    'transfer_type'
                                                ],

                                            'transfer_date' =>
                                                $data[
                                                    'transfer_date'
                                                ],

                                            'document_no' =>
                                                $data[
                                                    'document_no'
                                                ] ?? null,

                                            'document_file' =>
                                                $data[
                                                    'document_file'
                                                ] ?? null,

                                            'remarks' =>
                                                $data[
                                                    'remarks'
                                                ] ?? null,

                                            'created_by' =>
                                                auth()->id(),
                                        ]);

                                    /*
                                    |--------------------------------------------------------------------------
                                    | ৭. FROM Owner
                                    |--------------------------------------------------------------------------
                                    */

                                    $transfer->items()->create([

                                        'owner_type' =>
                                            FlatOwner::class,

                                        'owner_id' =>
                                            $currentOwner->id,

                                        'direction' =>
                                            'from',

                                        'ownership_percent' =>
                                            $transferPercent,
                                    ]);

                                    /*
                                    |--------------------------------------------------------------------------
                                    | ৮. Old Owner Ownership Reduce
                                    |--------------------------------------------------------------------------
                                    */

                                    $remainingPercent =
                                        $currentPercent -
                                        $transferPercent;

                                    if (
                                        $remainingPercent <= 0
                                    ) {

                                        $currentOwner->update([

                                            'ownership_percent' =>
                                                0,

                                            'end_date' =>
                                                $data[
                                                    'transfer_date'
                                                ],

                                            'is_current' =>
                                                false,
                                        ]);

                                    } else {

                                        $currentOwner->update([

                                            'ownership_percent' =>
                                                $remainingPercent,
                                        ]);
                                    }

                                    /*
                                    |--------------------------------------------------------------------------
                                    | ৯. Existing New Owner Search
                                    |--------------------------------------------------------------------------
                                    */

                                    $newOwner =
                                        FlatOwner::query()

                                            ->where(
                                                'flat_id',
                                                $currentOwner
                                                    ->flat_id
                                            )

                                            ->where(
                                                'user_id',
                                                $toUserId
                                            )

                                            ->where(
                                                'is_current',
                                                true
                                            )

                                            ->lockForUpdate()

                                            ->first();

                                    /*
                                    |--------------------------------------------------------------------------
                                    | ১০. Existing Owner হলে Ownership যোগ
                                    |--------------------------------------------------------------------------
                                    */

                                    if (
                                        $newOwner
                                    ) {

                                        $newOwner->update([

                                            'ownership_percent' =>
                                                (float)
                                                $newOwner
                                                    ->ownership_percent
                                                +
                                                $transferPercent,

                                            'is_current' =>
                                                true,

                                            'end_date' =>
                                                null,
                                        ]);

                                    } else {

                                        /*
                                        |--------------------------------------------------------------------------
                                        | ১১. নতুন FlatOwner
                                        |--------------------------------------------------------------------------
                                        */

                                        $newOwner =
                                            FlatOwner::create([

                                                'flat_id' =>
                                                    $currentOwner
                                                        ->flat_id,

                                                'user_id' =>
                                                    $toUserId,

                                                'ownership_percent' =>
                                                    $transferPercent,

                                                'start_date' =>
                                                    $data[
                                                        'transfer_date'
                                                    ],

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
                                    | ১২. TO Owner
                                    |--------------------------------------------------------------------------
                                    */

                                    $transfer->items()->create([

                                        'owner_type' =>
                                            FlatOwner::class,

                                        'owner_id' =>
                                            $newOwner->id,

                                        'direction' =>
                                            'to',

                                        'ownership_percent' =>
                                            $transferPercent,
                                    ]);
                                }
                            );
                        }
                    ),

                /*
                |--------------------------------------------------------------------------
                | DELETE
                |--------------------------------------------------------------------------
                */

                DeleteAction::make(),
            ])

            ->toolbarActions([
                BulkActionGroup::make([]),
            ]);
    }
}
