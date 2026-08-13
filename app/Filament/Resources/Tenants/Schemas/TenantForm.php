<?php

namespace App\Filament\Resources\Tenants\Schemas;

use App\Models\Flat;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TenantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                   
                    Step::make('ভাড়াটিয়ার তথ্য')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Select::make('flat_no')
                            ->label(__('formlabel.flat_no'))
                                ->options(function (?Model $record) {

                                    $currentFlatId = null;

                                    if ($record) {

                                        $agreement = $record->currentAgreement;

                                        $currentFlatId = $agreement?->occupancy?->flat_id;
                                    }

                                    $query = Flat::query()
                                        ->with(['floor.building.plot'])

                                        ->where(function ($q) use ($currentFlatId) {

                                            $q->whereDoesntHave('occupancies', function ($query) {
                                                $query->where('is_current', true);
                                            });

                                            if ($currentFlatId) {
                                                $q->orWhere('id', $currentFlatId);
                                            }
                                        });

                                    if (! auth()->user()->hasRole('super_admin')) {

                                        $query->whereHas('owners', function ($q) {
                                            $q->where('user_id', auth()->id())
                                            ->where('is_current', true);
                                        });
                                    }

                                    return $query->get()
                                        ->mapWithKeys(function ($flat) {

                                            return [
                                                $flat->id =>
                                                    $flat->floor?->building?->plot?->plot_no .
                                                    ' | ' .
                                                    $flat->floor?->building?->building_name .
                                                    ' | তলা-' .
                                                    $flat->floor?->floor_no .
                                                    ' | ফ্ল্যাট-' .
                                                    $flat->flat_no,
                                            ];
                                        });
                                })
                                ->afterStateHydrated(function ($component, $state, $record) {

                                    if ($record && blank($state)) {

                                        $component->state(
                                            $record->currentAgreement?->occupancy?->flat_id
                                        );
                                    }
                                })
                            ->required(),
                        TextInput::make('tenant_name')
                            ->label(__('formlabel.tenant_name'))
                            ->required(),
                        TextInput::make('father_name')
                            ->label(__('formlabel.father_name'))
                            ->required(),
                        TextInput::make('mother_name')
                        ->label(__('formlabel.mother_name'))
                            ->required(),
                        DatePicker::make('date_of_birth')
                            ->label(__('formlabel.date_of_birth'))
                            ->required(),
                        TextInput::make('nid_no')
                            ->label(__('formlabel.nid_no'))
                            ->required(),
                        TextInput::make('passport_no')
                            ->label(__('formlabel.passport_no'))
                            ->required(),
                        TextInput::make('mobile')
                            ->label(__('formlabel.mobile'))
                            ->required(),
                        TextInput::make('profession')
                            ->label(__('formlabel.profession'))
                            ->required(),
                        FileUpload::make('photo')
                            ->label(__('formlabel.photo'))
                            ->disk('public')
                            ->directory('images/tenants')
                    ])
                    ->columns(4),
                    Step::make('ভাড়াটিয়ার চুক্তিপত্র')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                         TextInput::make('agreement_no')
                            ->label('চুক্তি নম্বর')
                            ->required(),

                        DatePicker::make('agreement_start_date')
                            ->label('চুক্তি শুরুর তারিখ')
                            ->required(),

                        DatePicker::make('agreement_end_date')
                            ->label('চুক্তি শেষের তারিখ')
                            ->required(),

                        TextInput::make('monthly_rent')
                            ->label('মাসিক ভাড়া')
                            ->numeric(),

                        TextInput::make('security_deposit')
                            ->label('সিকিউরিটি ডিপোজিট')
                            ->numeric(),
                        Select::make('status')
                            ->label('চুক্তির অবস্থা')
                            ->options([
                                'active' => 'সক্রিয়',
                                'expired' => 'মেয়াদোত্তীর্ণ',
                                'terminated' => 'সমাপ্ত',
                                'renewed' => 'নবায়িত',
                            ])
                            ->afterStateHydrated(function ($component, $state, $record) {

                                if ($record && blank($state)) {

                                    $component->state(
                                        $record->currentAgreement?->status
                                    );
                                }
                            })
                            ->required(),
                        FileUpload::make('agreement_file')
                                ->label('চুক্তিপত্র')
                                ->disk('public')
                                ->directory('images/agreement_file')
                    ])
                    ->columns(4),
                    Step::make('জরুরি যোগাযোগ')
                    ->icon('heroicon-o-phone')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('name')
                                    ->label('নাম')
                                    ->required(),

                                TextInput::make('relation')
                                    ->label('সম্পর্ক')
                                    ->required(),

                                TextInput::make('mobile')
                                    ->label('মোবাইল')
                                    ->required(),
                            ]),

                        RichEditor::make('address')
                            ->label('ঠিকানা'),
                    ])
                    
                ])
                ->skippable()
                ->columnSpanFull(),
            ]);
    }
}
