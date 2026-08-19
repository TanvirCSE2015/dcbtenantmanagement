<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Models\OwnershipTransfer;
use App\Models\PlotOwner;
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
use Illuminate\Validation\ValidationException;

class PlotOwnershipsRelationManager extends RelationManager
{
    protected static string $relationship = 'plotOwnerships';

    protected static ?string $title = 'প্লট';

    protected static bool $isLazy = false;

    public static function getModelLabel(): string
    {
        return 'প্লট';
    }

    public static function getPluralModelLabel(): string
    {
        return 'প্লটসমূহ';
    }

     public function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                Select::make('plot_id')
                    ->label('প্লট')
                    ->relationship('plot', 'plot_no')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('ownership_percent')
                    ->label('মালিকানা %')
                    ->numeric()
                    ->minValue(0.01)
                    ->maxValue(function () {

                        $plotId = $this->data['plot_id'] ?? null;

                        if (! $plotId) {
                            return 100;
                        }

                        $currentTotal = PlotOwner::query()
                            ->where('plot_id', $plotId)
                            ->where('is_current', true)
                            ->sum('ownership_percent');

                        return max(0, 100 - $currentTotal);
                    })
                    ->default(function () {

                        $plotId = $this->data['plot_id'] ?? null;

                        if (! $plotId) {
                            return 100;
                        }

                        $currentTotal = PlotOwner::query()
                            ->where('plot_id', $plotId)
                            ->where('is_current', true)
                            ->sum('ownership_percent');

                        return max(0, 100 - $currentTotal);
                    })
                    ->required(),

                DatePicker::make('ownership_start_date')
                    ->label('মালিকানা শুরুর তারিখ')
                    ->default(now())
                    ->required(),

                DatePicker::make('ownership_end_date')
                    ->label('মালিকানা শেষের তারিখ'),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('plot_id')

            ->columns([

                TextColumn::make('plot.plot_no')
                    ->label('প্লট নং')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('plot.area.area_name')
                    ->label('এলাকা')
                    ->sortable(),

                TextColumn::make('ownership_percent')
                    ->label('মালিকানা %')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),

                TextColumn::make('ownership_start_date')
                    ->label('শুরু')
                    ->date(),

                TextColumn::make('ownership_end_date')
                    ->label('শেষ')
                    ->date()
                    ->placeholder('-'),

                IconColumn::make('is_current')
                    ->label('বর্তমান')
                    ->boolean(),

            ])

            ->headerActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus')

                    ->using(function (array $data) {

                        $plotId = $data['plot_id'];

                        $currentTotal = PlotOwner::query()
                            ->where('plot_id', $plotId)
                            ->where('is_current', true)
                            ->sum('ownership_percent');

                        $newTotal =
                            (float) $currentTotal +
                            (float) $data['ownership_percent'];

                        if ($newTotal > 100) {

                            throw ValidationException::withMessages([
                                'ownership_percent' =>
                                    "এই প্লটের বর্তমান মালিকানা {$currentTotal}%। "
                                    . "আর সর্বোচ্চ "
                                    . (100 - $currentTotal)
                                    . "% যোগ করা যাবে।",
                            ]);
                        }

                        return $this->getOwnerRecord()
                            ->plotOwnerships()
                            ->create([
                                'plot_id' =>
                                    $plotId,

                                'ownership_percent' =>
                                    $data['ownership_percent'],

                                'ownership_start_date' =>
                                    $data['ownership_start_date'],

                                'ownership_end_date' =>
                                    $data['ownership_end_date'] ?? null,

                                'is_current' =>
                                    true,

                                'created_by' =>
                                    auth()->id(),
                            ]);
                    }),
            ])

            ->recordActions([

                EditAction::make(),

                Action::make('transfer')
                    ->label('হস্তান্তর')
                    ->icon('heroicon-m-arrow-right')
                    ->color('warning')

                    ->visible(fn ($record) =>
                        $record->is_current
                    )

                    ->schema([

                        Grid::make(3)
                            ->schema([

                                Select::make('to_user_id')
                                    ->label('নতুন মালিক')
                                    ->options(function ($record) {

                                        return User::query()
                                            ->whereKeyNot(
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
                                                    ->unique(
                                                        table: 'users',
                                                        column: 'mobile'
                                                    ),

                                            ]),
                                    ])

                                    ->createOptionUsing(
                                        function (array $data): int {

                                            $user = User::create([
                                                'name' =>
                                                    $data['name'],

                                                'email' =>
                                                    $data['email'],

                                                'mobile' =>
                                                    $data['mobile'],

                                                'password' =>
                                                    bcrypt('12345678'),

                                                'is_active' =>
                                                    true,
                                            ]);

                                            return $user->id;
                                        }
                                    ),

                                TextInput::make('ownership_percent')
                                    ->label(
                                        'হস্তান্তরের পরিমাণ (%)'
                                    )
                                    ->numeric()
                                    ->minValue(0.01)
                                    ->maxValue(
                                        fn ($record) =>
                                            $record->ownership_percent
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
                                                    (float) $record->ownership_percent
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

                                Select::make('transfer_type')
                                    ->label('হস্তান্তরের ধরন')
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

                        Grid::make(3)
                            ->schema([

                                DatePicker::make('transfer_date')
                                    ->label('হস্তান্তরের তারিখ')
                                    ->default(now())
                                    ->required(),

                                TextInput::make('document_no')
                                    ->label('দলিল নম্বর'),

                                FileUpload::make('document_file')
                                    ->label('দলিল / PDF')
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
                            "প্লট মালিকানা হস্তান্তর — "
                            . "{$record->plot?->plot_no} — "
                            . "{$record->ownership_percent}%"
                    )

                    ->modalSubmitActionLabel(
                        'হস্তান্তর করুন'
                    )

                    ->modalCancelActionLabel(
                        'বাতিল'
                    )

                    ->modalWidth('4xl')

                    ->action(function (
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
                                | ১. Current owner lock
                                |--------------------------------------------------------------------------
                                */

                                $currentOwner =
                                    PlotOwner::query()
                                        ->whereKey(
                                            $record->id
                                        )
                                        ->lockForUpdate()
                                        ->firstOrFail();


                                /*
                                |--------------------------------------------------------------------------
                                | ২. Active কিনা
                                |--------------------------------------------------------------------------
                                */

                                if (
                                    ! $currentOwner->is_current
                                ) {

                                    throw ValidationException::withMessages([
                                        'ownership_percent' =>
                                            'এই মালিক বর্তমানে সক্রিয় মালিক নন।',
                                    ]);
                                }


                                /*
                                |--------------------------------------------------------------------------
                                | ৩. Transfer percentage
                                |--------------------------------------------------------------------------
                                */

                                $transferPercent =
                                    (float)
                                    $data['ownership_percent'];

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
                                | ৪. নতুন User
                                |--------------------------------------------------------------------------
                                */

                                $toUserId =
                                    (int)
                                    $data['to_user_id'];


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
                                | ৫. Plot
                                |--------------------------------------------------------------------------
                                */

                                $plot =
                                    $currentOwner->plot;


                                /*
                                |--------------------------------------------------------------------------
                                | ৬. Ownership Transfer Header
                                |--------------------------------------------------------------------------
                                */

                                $transfer =
                                    OwnershipTransfer::create([

                                        'ownable_type' =>
                                            $plot::class,

                                        'ownable_id' =>
                                            $plot->id,

                                        'transfer_type' =>
                                            $data['transfer_type'],

                                        'transfer_date' =>
                                            $data['transfer_date'],

                                        'document_no' =>
                                            $data['document_no']
                                            ?? null,

                                        'document_file' =>
                                            $data['document_file']
                                            ?? null,

                                        'remarks' =>
                                            $data['remarks']
                                            ?? null,

                                        'created_by' =>
                                            auth()->id(),
                                    ]);


                                /*
                                |--------------------------------------------------------------------------
                                | ৭. FROM item
                                |--------------------------------------------------------------------------
                                */

                                $transfer->items()->create([

                                    'owner_type' =>
                                        PlotOwner::class,

                                    'owner_id' =>
                                        $currentOwner->id,

                                    'direction' =>
                                        'from',

                                    'ownership_percent' =>
                                        $transferPercent,
                                ]);


                                /*
                                |--------------------------------------------------------------------------
                                | ৮. Old owner ownership কমানো
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

                                        'ownership_end_date' =>
                                            $data['transfer_date'],

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
                                | ৯. নতুন owner আগে আছে কিনা
                                |--------------------------------------------------------------------------
                                */

                                $newOwner =
                                    PlotOwner::query()
                                        ->where(
                                            'plot_id',
                                            $currentOwner->plot_id
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
                                | ১০. Existing owner
                                |--------------------------------------------------------------------------
                                */

                                if ($newOwner) {

                                    $newOwner->update([

                                        'ownership_percent' =>
                                            (float)
                                            $newOwner
                                                ->ownership_percent
                                            +
                                            $transferPercent,

                                    ]);

                                } else {

                                    /*
                                    |--------------------------------------------------------------------------
                                    | ১১. নতুন PlotOwner
                                    |--------------------------------------------------------------------------
                                    */

                                    $newOwner =
                                        PlotOwner::create([

                                            'plot_id' =>
                                                $currentOwner
                                                    ->plot_id,

                                            'user_id' =>
                                                $toUserId,

                                            'ownership_percent' =>
                                                $transferPercent,

                                            'ownership_start_date' =>
                                                $data['transfer_date'],

                                            'ownership_end_date' =>
                                                null,

                                            'is_current' =>
                                                true,

                                            'created_by' =>
                                                auth()->id(),

                                        ]);
                                }


                                /*
                                |--------------------------------------------------------------------------
                                | ১২. TO item
                                |--------------------------------------------------------------------------
                                */

                                $transfer->items()->create([

                                    'owner_type' =>
                                        PlotOwner::class,

                                    'owner_id' =>
                                        $newOwner->id,

                                    'direction' =>
                                        'to',

                                    'ownership_percent' =>
                                        $transferPercent,
                                ]);
                            }
                        );
                    }),

                DeleteAction::make(),

            ])

            ->toolbarActions([
                BulkActionGroup::make([]),
            ]);
    }
}
