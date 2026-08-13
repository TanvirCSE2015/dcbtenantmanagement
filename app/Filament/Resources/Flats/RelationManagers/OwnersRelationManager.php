<?php

namespace App\Filament\Resources\Flats\RelationManagers;

use App\Models\User;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

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
                TextInput::make('name')
                    ->label(__('formlabel.owner_name'))
                    ->required()
                    ->formatStateUsing(fn ($record) => $record?->user?->name),

                TextInput::make('email')
                    ->label('ইমেইল')
                    ->email()
                    ->required()
                    ->formatStateUsing(fn ($record) => $record?->user?->email),
                TextInput::make('ownership_percent')
                    ->label(__('formlabel.ownership_percent'))
                    ->required()
                    ->numeric()
                    ->default(100.0),
                Toggle::make('is_current')
                    ->label(__('formlabel.is_current'))
                    ->default(true)
                    ->required(),
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
                    ->using(function (array $data) {

                        $password = '12345678';

                        $user = User::create([
                            'name'      => $data['name'],
                            // 'mobile'    => $data['mobile'],
                            'email'     => $data['email'],
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
                                // 'ownership_start_date' => $data['ownership_start_date'],
                                'created_by' => auth()->id(),
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
                // DissociateAction::make(),
                 DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // DissociateBulkAction::make(),
                    // DeleteBulkAction::make(),
                ]),
            ]);
    }
}
