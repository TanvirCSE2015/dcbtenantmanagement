<?php

namespace App\Filament\Resources\Tenants\Schemas;

use App\Models\Flat;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

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
                        Fieldset::make()
                        ->schema([
                            Select::make('occupancy_type')
                                ->label('বসতির ধরন')
                                ->options([
                                    'tenant' => 'ভাড়াটিয়া',
                                    'owner'  => 'নিজ বসতি',
                                ])
                                ->live()
                                ->required(),
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
                        ])
                        ->columns(5)
                        ->columnSpanFull(),
                        Fieldset::make()
                        ->schema([
                            DatePicker::make('date_of_birth')
                                ->label(__('formlabel.date_of_birth'))
                                ->required(),
                            TextInput::make('nid_no')
                                ->label(__('formlabel.nid_no'))
                                ->required(),
                            TextInput::make('passport_no')
                                ->label(__('formlabel.passport_no'))
                                ->required(),
                            Select::make('marital_status') 
                                ->label(__('formlabel.marital_status'))
                                ->options([
                                    'married'=>'বিবাহিত',
                                    'unmarried'=>'অবিবাহিত'
                                ]),
                            Select::make('religion') 
                                ->label(__('formlabel.religion'))
                                ->options([
                                    'ইসলাম'=>'ইসলাম',
                                    'সনাতন' => 'সনাতন',
                                    'বৌদ্ধ'=>'বৌদ্ধ',
                                    'খ্রীস্টান' => 'খ্রীস্টান'
                                ])
                                ->default('ইসলাম'),
                            
                        ])
                        ->columns(5)
                        ->columnSpanFull(),
                         Fieldset::make()
                        ->schema([
                            TextInput::make('birth_place')
                                 ->label(__('formlabel.birth_place')),
                            TextInput::make('mobile')
                                ->label(__('formlabel.mobile'))
                                ->required(),
                            TextInput::make('email') 
                                ->label(__('formlabel.email')),
                            
                            TextInput::make('education') 
                                ->label(__('formlabel.education'))
                                ->required(),
                            // TextInput::make('profession')
                            //     ->label(__('formlabel.profession'))
                            //     ->required(),
                            FileUpload::make('photo')
                                ->label(__('formlabel.photo'))
                                ->disk('public')
                                ->directory('images/tenants')
                         ])
                         ->columns(5)
                        ->columnSpanFull(),
                    ])
                    ->columns(4),
                    Step::make('ভাড়াটিয়ার চুক্তিপত্র')
                    ->icon('heroicon-o-document-text')
                    ->visible(fn (Get $get) =>
                        $get('occupancy_type') === 'tenant'
                    )
                    ->schema([
                         TextInput::make('agreement_no')
                            ->label('চুক্তি নম্বর')
                            ->required()
                            ->default(function () {

                                do {
                                    $agreementNo = now()->format('Ym')
                                        .rand(1000, 9999);
                                } while (
                                    \App\Models\RentalAgreement::where(
                                        'agreement_no',
                                        $agreementNo
                                    )->exists()
                                );

                                return $agreementNo;
                            })
                            ->disabled()
                            ->dehydrated()
                            ->rules(function ($record) {
                                return [
                                    Rule::unique('rental_agreements', 'agreement_no')
                                        ->ignore(
                                            $record?->currentAgreement?->id
                                        ),
                                ];
                            }),

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

                                TextInput::make('e_mobile')
                                    ->label('মোবাইল')
                                    ->required(),
                            ]),

                        RichEditor::make('address')
                            ->label('ঠিকানা'),
                    ]),

                Step::make("পেশা ও অন্যান্য তথ্য")
                    ->icon(Heroicon::AcademicCap)
                    ->schema([
                        Fieldset::make('পেশাগত তথ্য')
                           ->schema([
                                TextInput::make('title_p')
                                    ->label(__('formlabel.title_p'))
                                    ->required(),
                                TextInput::make('office_name')
                                    ->label(__('formlabel.office_name'))
                                    ->required(),
                                TextInput::make('designation')
                                    ->label(__('formlabel.designation'))
                                    ->required(),
                                TextInput::make('mobile_no')
                                    ->label(__('formlabel.mobile_no'))
                                    ->required(),
                                Textarea::make('office_address')
                                    ->label(__('formlabel.office_address'))
                                    ->required(),
                           ])
                           ->columns(5),
                        Fieldset::make('অন্যান্য তথ্য')
                            ->schema([
                                Textarea::make('old_rental')
                                    ->label('পূর্বের বাসস্থানের ঠিকানা'),
                                Textarea::make('old_flat_owner')
                                    ->label('পূর্বের বাড়ির মালিকের নাম ও মোবাইল নম্বরনা'),
                                Textarea::make('current_case')
                                    ->label('কোন মামলা/অপরাধে পূর্বে গ্রেফতার বা দণ্ডপ্রাপ্ত কিনা'),
                            ])
                            ->columns(3)

                    ]),
                    
                ])
                ->skippable()
                ->columnSpanFull(),
            ]);
    }
}
