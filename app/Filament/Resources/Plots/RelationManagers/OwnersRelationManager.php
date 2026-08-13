<?php

namespace App\Filament\Resources\Plots\RelationManagers;

use App\Filament\Resources\Buildings\BuildingResource;
use App\Filament\Resources\Plots\PlotResource;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

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

            // TextInput::make('mobile')
            //     ->label('মোবাইল')
            //     ->required()
            //     ->unique(ignoreRecord: true),

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
                            'name'      => $data['user']['name'],
                            // 'mobile'    => $data['mobile'],
                            'email'     => $data['user']['email'],
                            // 'nid'       => $data['user']['nid'] ?? null,
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
                        ]);

                        $record->update([
                            'ownership_percent' => $data['ownership_percent'],
                            'ownership_start_date' => $data['ownership_start_date'],
                        ]);

                        return $record;
                    }),
            ]);
    }
}
