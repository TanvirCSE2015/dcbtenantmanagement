<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                Step::make('মালিকের তথ্য')
                    ->icon(Heroicon::User)
                    ->schema([

                        Grid::make(4)
                            ->schema([

                                TextInput::make('name')
                                    ->label('নাম')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('mobile')
                                    ->label('মোবাইল')
                                    ->tel()
                                    ->required()
                                    ->unique(
                                        table: 'users',
                                        column: 'mobile',
                                        ignoreRecord: true,
                                    ),

                                

                                TextInput::make('nid')
                                    ->label('এনআইডি')
                                    ->maxLength(50),

                                // DatePicker::make('date_of_birth')
                                //     ->label('জন্ম তারিখ'),

                                Toggle::make('is_active')
                                    ->label('সক্রিয়')
                                    ->default(true),
                                FileUpload::make('photo')
                                ->label('ছবি')
                                ->image()
                                ->disk('public')
                                ->directory('images/users')
                                ->imageEditor(),

                            ]),

                        

                    ]),

                Step::make('লগইন তথ্য')
                    ->icon(Heroicon::LockOpen)
                    ->schema([

                        TextInput::make('email')
                            ->label('ইমেইল')
                            ->email()
                            ->required()
                            ->unique(
                                table: 'users',
                                column: 'email',
                                ignoreRecord: true,
                            ),

                        TextInput::make('password')
                            ->label('পাসওয়ার্ড')
                            ->password()
                            ->revealable()
                            ->required(fn ($operation) => $operation === 'create')
                            ->dehydrated(fn ($state) => filled($state))
                            ->minLength(8),

                        TextInput::make('password_confirmation')
                            ->label('পাসওয়ার্ড নিশ্চিত করুন')
                            ->password()
                            ->revealable()
                            ->same('password')
                            ->required(fn ($operation) => $operation === 'create')
                            ->dehydrated(false),

                    ])
                    ->columns(3),
                ])
                ->skippable()
                ->columnSpanFull()

            ]);
    }
}
