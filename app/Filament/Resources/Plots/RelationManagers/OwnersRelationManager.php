<?php

namespace App\Filament\Resources\Plots\RelationManagers;

use App\Filament\Resources\Buildings\BuildingResource;
use App\Filament\Resources\Plots\PlotResource;
use App\Models\OwnershipTransfer;
use App\Models\PlotOwner;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OwnersRelationManager extends RelationManager
{
    protected static string $relationship = 'owners';

    protected static ?string $relatedResource = PlotResource::class;

    protected static ?string $title = 'প্লটের মালিক';

    protected static bool $isLazy = false;

    public static function getModelLabel(): string
    {
        return ('মালিক');
    }

    public static function getPluralModelLabel(): string
    {
        return ('মালিকগণ');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('name')
                    ->label('নাম')
                    ->required()
                    ->formatStateUsing(fn ($record) => $record?->user?->name),

                TextInput::make('email')
                    ->label('ইমেইল')
                    ->email()
                    ->required()
                    ->formatStateUsing(fn ($record) => $record?->user?->email),

                TextInput::make('mobile')
                    ->label('মোবাইল')
                    ->required()
                    ->unique(
                        table: 'users',
                        column: 'mobile'
                    )
                    ->formatStateUsing(fn ($record) => $record?->user?->mobile),

            // TextInput::make('nid')
            //     ->label('এনআইডি'),

            TextInput::make('ownership_percent')
                ->label('মালিকানার %')
                ->numeric()
                ->default(100)
                ->required(),

            DatePicker::make('ownership_start_date')
                ->label('মালিকানা শুরুর তারিখ')
                ->default(now())
                ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label(__('formlabel.name')),
                TextColumn::make('ownership_percent')
                    ->label('মালিকানা %'),

                IconColumn::make('is_current')
                    ->label('বর্তমান')
                    ->boolean(),
                
            ])
            ->headerActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus')
                    ->using(function (array $data) {

                        $password = '12345678';

                        $user = User::create([
                            'name'      => $data['name'],
                            'email'     => $data['email'],
                            'mobile'    => $data['mobile'],
                            'password'  => bcrypt($password),
                            'is_active' => true,
                        ]);

                        // $user->assignRole('Plot Owner');

                        return $this->getOwnerRecord()
                            ->owners()
                            ->create([
                                'user_id' => $user->id,
                                'ownership_percent' => $data['ownership_percent'],
                                'ownership_start_date' => $data['ownership_start_date'],
                                'created_by' => auth()->id(),
                            ]);
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->using(function ($record, array $data) {

                        $record->user->update([
                            'name' => $data['name'],
                            'email' => $data['email'],
                            'mobile'    => $data['mobile'],
                        ]);

                        $record->update([
                            'ownership_percent' => $data['ownership_percent'],
                            'ownership_start_date' => $data['ownership_start_date'],
                        ]);

                        return $record;
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
                                                ->where('id', '!=', $record->user_id)
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
                                        }),


                                    TextInput::make('ownership_percent')
                                        ->label('হস্তান্তরের পরিমাণ (%)')
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
                                                            "হস্তান্তরের পরিমাণ {$record->ownership_percent}% এর বেশি হতে পারবে না।"
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
                                        ->directory('ownership-transfers')
                                        ->acceptedFileTypes([
                                            'application/pdf',
                                            'image/jpeg',
                                            'image/png',
                                        ])
                                        ->maxSize(10240),

                                ]),

                        ])

                        ->modalHeading(
                            fn ($record) =>
                                "মালিকানা হস্তান্তর —> {$record->user?->name} —> {$record->plot->area->area_name} —> {$record->plot->plot_no}
                                 —> {$record->ownership_percent} %"
                        )

                        ->modalSubmitActionLabel(
                            'হস্তান্তর করুন'
                        )

                        ->modalCancelActionLabel(
                            'বাতিল'
                        )

                        ->modalWidth('4xl')
                        ->action(function ($record, array $data) {

                            DB::transaction(function () use ($record, $data) {

                                /*
                                |--------------------------------------------------------------------------
                                | ১. বর্তমান owner lock করা
                                |--------------------------------------------------------------------------
                                */

                                $currentOwner = PlotOwner::query()
                                    ->whereKey($record->id)
                                    ->lockForUpdate()
                                    ->firstOrFail();


                                /*
                                |--------------------------------------------------------------------------
                                | ২. নিশ্চিত করা হচ্ছে owner বর্তমানে active
                                |--------------------------------------------------------------------------
                                */

                                if (! $currentOwner->is_current) {

                                    throw ValidationException::withMessages([
                                        'ownership_percent' =>
                                            'এই মালিক বর্তমানে আর সক্রিয় মালিক নন।',
                                    ]);
                                }


                                /*
                                |--------------------------------------------------------------------------
                                | ৩. Transfer percentage validation
                                |--------------------------------------------------------------------------
                                */

                                $transferPercent =
                                    (float) $data['ownership_percent'];

                                $currentPercent =
                                    (float) $currentOwner->ownership_percent;


                                if ($transferPercent <= 0) {

                                    throw ValidationException::withMessages([
                                        'ownership_percent' =>
                                            'হস্তান্তরের পরিমাণ ০ এর বেশি হতে হবে।',
                                    ]);
                                }


                                if ($transferPercent > $currentPercent) {

                                    throw ValidationException::withMessages([
                                        'ownership_percent' =>
                                            "হস্তান্তরের পরিমাণ {$currentPercent}% এর বেশি হতে পারবে না।",
                                    ]);
                                }


                                /*
                                |--------------------------------------------------------------------------
                                | ৪. নতুন মালিক
                                |--------------------------------------------------------------------------
                                */

                                $toUserId = (int) $data['to_user_id'];


                                /*
                                |--------------------------------------------------------------------------
                                | ৫. নিজের কাছে transfer করা যাবে না
                                |--------------------------------------------------------------------------
                                */

                                if ($toUserId === (int) $currentOwner->user_id) {

                                    throw ValidationException::withMessages([
                                        'to_user_id' =>
                                            'বর্তমান মালিককে নতুন মালিক হিসেবে নির্বাচন করা যাবে না।',
                                    ]);
                                }


                                /*
                                |--------------------------------------------------------------------------
                                | ৬. Plot বের করা
                                |--------------------------------------------------------------------------
                                */

                                $plot = $this->getOwnerRecord();


                                /*
                                |--------------------------------------------------------------------------
                                | ৭. Ownership Transfer Header তৈরি
                                |--------------------------------------------------------------------------
                                */

                                $transfer = OwnershipTransfer::create([

                                    'ownable_type' => $plot::class,

                                    'ownable_id' => $plot->id,

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
                                | ৮. FROM owner item
                                |--------------------------------------------------------------------------
                                */

                                $transfer->items()->create([

                                    'owner_type' => PlotOwner::class,

                                    'owner_id' => $currentOwner->id,

                                    'direction' => 'from',

                                    'ownership_percent' =>
                                        $transferPercent,
                                ]);


                                /*
                                |--------------------------------------------------------------------------
                                | ৯. পুরোনো মালিকের ownership কমানো
                                |--------------------------------------------------------------------------
                                */

                                $remainingPercent =
                                    $currentPercent - $transferPercent;


                                if ($remainingPercent <= 0) {

                                    /*
                                    * সম্পূর্ণ ownership transfer
                                    */

                                    $currentOwner->update([

                                        'ownership_percent' => 0,

                                        'ownership_end_date' =>
                                            $data['transfer_date'],

                                        'is_current' => false,
                                    ]);

                                } else {

                                    /*
                                    * Partial ownership transfer
                                    */

                                    $currentOwner->update([

                                        'ownership_percent' =>
                                            $remainingPercent,
                                    ]);
                                }


                                /*
                                |--------------------------------------------------------------------------
                                | ১০. নতুন মালিকের existing ownership খোঁজা
                                |--------------------------------------------------------------------------
                                */

                                $newOwner = PlotOwner::query()
                                    ->where('plot_id', $currentOwner->plot_id)
                                    ->where('user_id', $toUserId)
                                    ->where('is_current', true)
                                    ->lockForUpdate()
                                    ->first();


                                /*
                                |--------------------------------------------------------------------------
                                | ১১. নতুন মালিক আগে থেকেই owner হলে
                                |--------------------------------------------------------------------------
                                */

                                if ($newOwner) {

                                    $newOwner->update([

                                        'ownership_percent' =>
                                            (float) $newOwner->ownership_percent
                                            + $transferPercent,
                                    ]);

                                } else {

                                    /*
                                    |--------------------------------------------------------------------------
                                    | ১২. নতুন PlotOwner তৈরি
                                    |--------------------------------------------------------------------------
                                    */

                                    $newOwner = PlotOwner::create([

                                        'plot_id' =>
                                            $currentOwner->plot_id,

                                        'user_id' =>
                                            $toUserId,

                                        'ownership_percent' =>
                                            $transferPercent,

                                        'ownership_start_date' =>
                                            $data['transfer_date'],

                                        'ownership_end_date' => null,

                                        'is_current' => true,

                                        'created_by' =>
                                            auth()->id(),
                                    ]);
                                }


                                /*
                                |--------------------------------------------------------------------------
                                | ১৩. TO owner item
                                |--------------------------------------------------------------------------
                                */

                                $transfer->items()->create([

                                    'owner_type' => PlotOwner::class,

                                    'owner_id' => $newOwner->id,

                                    'direction' => 'to',

                                    'ownership_percent' =>
                                        $transferPercent,
                                ]);
                            });
                        }),
            ]);
    }
}
