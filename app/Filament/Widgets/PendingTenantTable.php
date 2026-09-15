<?php

namespace App\Filament\Widgets;

use App\Models\Tenant;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class PendingTenantTable extends TableWidget
{
    protected static bool $isLazy = false;

    protected function getTableHeading(): string { 
        $count = Tenant::query() ->where('status', 'pending') ->count(); 
        return "নতুন বসবাসকারী (" . $this->en2bn($count) . ")";
    }

    protected function getTableEmptyStateHeading(): string { 
        return 'কোনো নতুন বসবাসকারী নেই'; 
    }

    protected function getTableEmptyStateDescription(): ?string { 
        return 'নতুন বসবাসকারী তালিকাভুক্ত হলে এখানে প্রদর্শিত হবে।'; 
    }

    protected function en2bn($number) { 
        $en = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9']; 
        $bn = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯']; 
        return str_replace($en, $bn, $number); 
    }
    // protected static ?string $heading = 'নতুন বসবাসকারী';
    protected static ?int $sort = 2; 
    protected int|string|array $columnSpan = 'full';
    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Tenant::query()
            ->with([ 'rentalAgreements.occupancy.flat.floor.building.plot.area', ])
            ->where('status', 'pending'))
            ->extraAttributes([ 'class' => 'pending-tenant-table-3d', ])
            ->columns([
                TextColumn::make('tenant_name')
                ->label(__('formlabel.tenant_name'))
                ->searchable()
                ->sortable(),

                // TextColumn::make('father_name')
                //     ->label(__('formlabel.father_name'))
                //     ->searchable(),

                TextColumn::make('mobile')
                    ->label(__('formlabel.mobile'))
                    ->searchable(),

                TextColumn::make(
                    'currentAgreement.occupancy.flat.floor.building.plot.area.area_name'
                )
                    ->label(__('formlabel.area')),

                TextColumn::make(
                    'currentAgreement.occupancy.flat.floor.building.plot.plot_no'
                )
                ->label(__('formlabel.plot_no')),

                TextColumn::make(
                'currentAgreement.occupancy.flat.flat_no'
            )
                ->label(__('formlabel.flat_no')),
            TextColumn::make('currentAgreement.occupancy.occupancy_type')
                ->label(__('formlabel.occupancy_type'))
                ->searchable()
                ->formatStateUsing(fn ($state) => match ($state) {
                    'tenant' => 'ভাড়াটিয়া',
                    'owner' => 'নিজ বসতি',
                    default => $state,
                })
                ->badge()
                ->color(fn ($state) => match ($state) {
                    'tenant' => 'info',
                    'owner' => 'success',
                    default => 'gray',
                }),
            TextColumn::make('status') 
            ->label('স্ট্যাটাস') ->badge() 
            ->formatStateUsing( fn (string $state): string => match ($state) { 'pending' => 'পেন্ডিং', 'approved' => 'অনুমোদিত', 'rejected' => 'বাতিল', default => $state, } ) 
            ->color( fn (string $state): string => match ($state) 
            { 'pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger', default => 'gray', } ),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            // ->recordActions([
            //     Action::make('update_status') 
            //     ->label('') 
            //     ->tooltip('স্ট্যাটাস পরিবর্তন করুন')
            //     ->icon('heroicon-m-pencil-square') 
            //     ->color('primary') 
            //     ->requiresConfirmation()
            //     ->schema([ Select::make('status') ->label('স্ট্যাটাস') 
            //     ->options([ 'pending' => 'পেন্ডিং', 'approved' => 'অনুমোদিত', 'rejected' => 'বাতিল', ]) 
            //     ->default(fn (Tenant $record) => $record->status) ->required(), ]) 
            //     ->modalHeading('বসবাসকারীর স্ট্যাটাস পরিবর্তন করুন') ->modalSubmitActionLabel('স্ট্যাটাস আপডেট করুন') 
            //     ->modalCancelActionLabel('বাতিল') 
            //     ->action(function ( Tenant $record, array $data ): void { 
            //         $record->update([ 'status' => $data['status'], ]); 
            //     }),
                
            // ])
            ->recordActions([

                Action::make('view')
                    ->label('বিস্তারিত')
                    ->tooltip('বিস্তারিত দেখুন')
                    ->icon('heroicon-m-eye')
                    ->color('info')
                    ->modalWidth('6xl')
                    ->modalHeading('বসবাসকারীর বিস্তারিত তথ্য')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('বন্ধ করুন')

                    ->schema(function (Tenant $record) {

                        $agreement = $record->currentAgreement;

                        $occupancy = $agreement?->occupancy;

                        $flat = $occupancy?->flat;

                        $floor = $flat?->floor;

                        $building = $floor?->building;

                        $plot = $building?->plot;

                        $area = $plot?->area;

                        $owner = $flat?->currentOwners?->first();

                        return [

                            /*
                            |--------------------------------------------------------------------------
                            | Location
                            |--------------------------------------------------------------------------
                            */

                            Grid::make(4)
                                ->schema([

                                    // =================================
                                    // বাম পাশে — ২/৩ অংশ
                                    // =================================

                                    Section::make('বাসার তথ্য')
                                        ->icon('heroicon-o-home')
                                        ->columns(4)
                                        ->schema([

                                            TextEntry::make('area')
                                                ->label('এলাকা')
                                                ->state(fn (Tenant $record) =>
                                                    $record->currentAgreement?->occupancy?->flat?->floor?->building?->plot?->area?->area_name
                                                    ?? 'N/A'
                                                ),

                                            TextEntry::make('plot')
                                                ->label('প্লট নং')
                                                ->state(fn (Tenant $record) =>
                                                    $record->currentAgreement?->occupancy?->flat?->floor?->building?->plot?->plot_no
                                                    ?? 'N/A'
                                                ),

                                            TextEntry::make('building')
                                                ->label('বিল্ডিং')
                                                ->state(fn (Tenant $record) =>
                                                    $record->currentAgreement?->occupancy?->flat?->floor?->building?->building_name
                                                    ?? 'N/A'
                                                ),

                                            TextEntry::make('floor')
                                                ->label('তলা')
                                                ->state(fn (Tenant $record) =>
                                                    ($floorNo = $record->currentAgreement?->occupancy?->flat?->floor?->floor_no)
                                                        ? self::floorOrdinal($floorNo)
                                                        : 'N/A'
                                                ),

                                            TextEntry::make('flat')
                                                ->label('ফ্ল্যাট নং')
                                                ->state(fn (Tenant $record) =>
                                                    $record->currentAgreement?->occupancy?->flat?->flat_no
                                                    ?? 'N/A'
                                                ),

                                            TextEntry::make('flat_side')
                                                ->label('ফ্ল্যাটের পাশ')
                                                ->state(fn (Tenant $record) =>
                                                    $record->currentAgreement?->occupancy?->flat?->flat_side
                                                    ?? 'N/A'
                                                ),

                                            TextEntry::make('flat_area')
                                                ->label('ফ্ল্যাটের আয়তন')
                                                ->state(fn (Tenant $record) =>
                                                    $record->currentAgreement?->occupancy?->flat?->flat_area
                                                    ?? 'N/A'
                                                ),

                                            TextEntry::make('road')
                                                ->label('রাস্তা')
                                                ->state(fn (Tenant $record) =>
                                                    $record->currentAgreement?->occupancy?->flat?->floor?->building?->plot?->road_no
                                                    ?? 'N/A'
                                                ),

                                        ])
                                        ->columnSpan(3),


                                    // =================================
                                    // ডান পাশে — ১/৩ অংশ
                                    // =================================
                                    Section::make('বসবাসকারীর ছবি')
                                        ->icon('heroicon-o-camera')
                                        ->schema([
                                             ViewEntry::make('tenant_photo')
                                                ->view('filament.infolists.tenant-photo')
                                                ->columnSpanFull(),
                                        ])
                                        ->columnSpan(1),

                                ])
                                ->columnSpanFull(),
                            Section::make('বসবাসকারীর ব্যক্তিগত তথ্য')
                                ->icon('heroicon-o-user')
                                ->columns(4)
                                ->schema([

                                    TextEntry::make('tenant_name')
                                        ->label('বসবাসকারীর নাম')
                                        ->state($record->tenant_name),

                                    TextEntry::make('father_name')
                                        ->label('পিতার নাম')
                                        ->state($record->father_name ?? 'N/A'),

                                    TextEntry::make('mother_name')
                                        ->label('মাতার নাম')
                                        ->state($record->mother_name ?? 'N/A'),

                                    TextEntry::make('date_of_birth')
                                        ->label('জন্ম তারিখ')
                                        ->state(
                                            $record->date_of_birth
                                                ? \Carbon\Carbon::parse(
                                                    $record->date_of_birth
                                                )->format('d-m-Y')
                                                : 'N/A'
                                        ),

                                    TextEntry::make('birth_place')
                                        ->label('জন্মস্থান')
                                        ->state($record->birth_place ?? 'N/A'),

                                    TextEntry::make('marital_status')
                                        ->label('বৈবাহিক অবস্থা')
                                        ->state(
                                            match ($record->marital_status) {
                                                'married' => 'বিবাহিত',
                                                'unmarried' => 'অবিবাহিত',
                                                default => 'N/A',
                                            }
                                        ),

                                    TextEntry::make('religion')
                                        ->label('ধর্ম')
                                        ->state($record->religion ?? 'N/A'),

                                    TextEntry::make('education')
                                        ->label('শিক্ষাগত যোগ্যতা')
                                        ->state($record->education ?? 'N/A'),

                                    TextEntry::make('mobile')
                                        ->label('মোবাইল নম্বর')
                                        ->state($record->mobile ?? 'N/A'),

                                    TextEntry::make('email')
                                        ->label('ই-মেইল')
                                        ->state($record->email ?? 'N/A'),

                                    TextEntry::make('nid_no')
                                        ->label('জাতীয় পরিচয়পত্র')
                                        ->state($record->nid_no ?? 'N/A'),

                                    TextEntry::make('passport_no')
                                        ->label('পাসপোর্ট নম্বর')
                                        ->state($record->passport_no ?? 'N/A'),

                                    TextEntry::make('permanent_current_address')
                                        ->label('পৈতৃক ও বর্তমান ঠিকানা')
                                        ->state(
                                            $record->permanent_current_address ?? 'N/A'
                                        )
                                        ->columnSpanFull(),

                                ]),

                            /*
                            |--------------------------------------------------------------------------
                            | Profession
                            |--------------------------------------------------------------------------
                            */

                            Section::make('পেশাগত তথ্য')
                                ->icon('heroicon-o-briefcase')
                                ->columns(4)
                                ->schema([

                                    TextEntry::make('profession')
                                        ->label('পেশা')
                                        ->state(
                                            $agreement?->profession?->title_p ?? 'N/A'
                                        ),

                                    TextEntry::make('office_name')
                                        ->label('অফিসের নাম')
                                        ->state(
                                            $agreement?->profession?->office_name ?? 'N/A'
                                        ),

                                    TextEntry::make('designation')
                                        ->label('পদবী')
                                        ->state(
                                            $agreement?->profession?->designation ?? 'N/A'
                                        ),

                                    TextEntry::make('office_mobile')
                                        ->label('অফিসের মোবাইল')
                                        ->state(
                                            $agreement?->profession?->mobile_no ?? 'N/A'
                                        ),

                                    TextEntry::make('office_address')
                                        ->label('অফিসের ঠিকানা')
                                        ->state(
                                            $agreement?->profession?->office_address ?? 'N/A'
                                        )
                                        ->columnSpanFull(),

                                ])
                                ->collapsible()
                                ->collapsed(),

                            /*
                            |--------------------------------------------------------------------------
                            | Agreement
                            |--------------------------------------------------------------------------
                            */

                            Section::make('ভাড়ার চুক্তির তথ্য')
                                ->icon('heroicon-o-document-text')
                                ->columns(3)
                                ->schema([

                                    TextEntry::make('agreement_no')
                                        ->label('চুক্তি নং')
                                        ->state($agreement?->agreement_no ?? 'N/A'),

                                    TextEntry::make('agreement_start_date')
                                        ->label('চুক্তি শুরুর তারিখ')
                                        ->state(
                                            $agreement?->agreement_start_date
                                                ? \Carbon\Carbon::parse(
                                                    $agreement->agreement_start_date
                                                )->format('d-m-Y')
                                                : 'N/A'
                                        ),

                                    TextEntry::make('agreement_end_date')
                                        ->label('চুক্তি শেষের তারিখ')
                                        ->state(
                                            $agreement?->agreement_end_date
                                                ? \Carbon\Carbon::parse(
                                                    $agreement->agreement_end_date
                                                )->format('d-m-Y')
                                                : 'N/A'
                                        ),

                                    TextEntry::make('monthly_rent')
                                        ->label('মাসিক ভাড়া')
                                        ->state(
                                            $agreement?->monthly_rent
                                                ? number_format(
                                                    $agreement->monthly_rent,
                                                    2
                                                ) . ' টাকা'
                                                : 'N/A'
                                        ),

                                    TextEntry::make('security_deposit')
                                        ->label('জামানত')
                                        ->state(
                                            $agreement?->security_deposit
                                                ? number_format(
                                                    $agreement->security_deposit,
                                                    2
                                                ) . ' টাকা'
                                                : 'N/A'
                                        ),

                                    TextEntry::make('agreement_status')
                                        ->label('চুক্তির স্ট্যাটাস')
                                        ->badge()
                                        ->state(
                                            match ($agreement?->status) {
                                                'active' => 'সক্রিয়',
                                                'pending' => 'পেন্ডিং',
                                                'expired' => 'মেয়াদ শেষ',
                                                'terminated' => 'বাতিল',
                                                default => 'N/A',
                                            }
                                        ),

                                ])
                                ->visible(
                                    fn () => $agreement?->occupancy?->occupancy_type !== 'owner'
                                )
                                ->collapsible()
                                ->collapsed(),

                            /*
                            |--------------------------------------------------------------------------
                            | House Owner
                            |--------------------------------------------------------------------------
                            */

                            Section::make('বাড়ির মালিকের তথ্য')
                                ->icon('heroicon-o-user-circle')
                                ->columns(3)
                                ->schema([

                                    TextEntry::make('owner_name')
                                        ->label('মালিকের নাম')
                                        ->state(
                                            $owner?->user?->name ?? 'N/A'
                                        ),

                                    TextEntry::make('owner_mobile')
                                        ->label('মোবাইল নম্বর')
                                        ->state(
                                            $owner?->user?->mobile ?? 'N/A'
                                        ),

                                    TextEntry::make('owner_nid')
                                        ->label('জাতীয় পরিচয়পত্র')
                                        ->state(
                                            $owner?->user?->nid ?? 'N/A'
                                        ),

                                ])
                                ->collapsible()
                                ->collapsed(),

                            /*
                            |--------------------------------------------------------------------------
                            | Family Members
                            |--------------------------------------------------------------------------
                            */

                            Section::make('পরিবারের সদস্য')
                                ->icon('heroicon-o-users')
                                ->schema([

                                    RepeatableEntry::make('tenantFamilyMembers')
                                        ->label('সদস্যদের বিস্তারিত তথ্য')
                                        ->schema([
                                            TextEntry::make('name')
                                                ->label('নাম'),

                                            TextEntry::make('relation')
                                                ->label('সম্পর্ক'),

                                            TextEntry::make('mobile')
                                                ->label('মোবাইল'),
                                        ])
                                        ->columns(3),

                                ])
                                ->collapsible()
                                ->collapsed(),

                            /*
                            |--------------------------------------------------------------------------
                            | Vehicles
                            |--------------------------------------------------------------------------
                            */

                            Section::make('গাড়ির তথ্য')
                                ->icon('heroicon-o-truck')
                                ->schema([

                                    RepeatableEntry::make('vechicles')
                                        ->label('গাড়ির বিস্তারিত তথ্য')
                                        ->schema([

                                            TextEntry::make('vehicle_type')
                                                ->label('গাড়ির ধরন'),

                                            TextEntry::make('brand')
                                                ->label('ব্র্যান্ড'),

                                            TextEntry::make('model')
                                                ->label('মডেল'),

                                            TextEntry::make('registration_no')
                                                ->label('রেজিস্ট্রেশন'),

                                            TextEntry::make('color')
                                                ->label('রং'),

                                        ])
                                        ->columns(5)
                                        ->columnSpanFull(),

                                ])
                                ->collapsible()
                                ->collapsed(),

                            /*
                            |--------------------------------------------------------------------------
                            | Driver
                            |--------------------------------------------------------------------------
                            */

                            Section::make('ড্রাইভারের তথ্য')
                                ->icon('heroicon-o-identification')
                                ->schema([

                                    RepeatableEntry::make('driverAssignments')
                                        ->label('ড্রাইভারের বিস্তারিত তথ্য')
                                        ->schema([

                                            TextEntry::make('staff.full_name')
                                                ->label('নাম'),

                                            TextEntry::make('staff.mobile')
                                                ->label('মোবাইল'),

                                            TextEntry::make('staff.nid_no')
                                                ->label('NID'),

                                            TextEntry::make('vechicle.registration_no')
                                                ->label('গাড়ির নম্বর'),

                                        ])
                                        ->columns(4)
                                        ->columnSpanFull(),

                                ])
                                ->collapsible()
                                ->collapsed(),

                            /*
                            |--------------------------------------------------------------------------
                            | Housemaid
                            |--------------------------------------------------------------------------
                            */

                            Section::make('গৃহকর্মীর তথ্য')
                                ->icon('heroicon-o-user')
                                ->schema([

                                    RepeatableEntry::make('houseMaidAssignments')
                                        ->label('গৃহকর্মীর বিস্তারিত তথ্য')
                                        ->schema([

                                            TextEntry::make('staff.full_name')
                                                ->label('নাম'),

                                            TextEntry::make('staff.mobile')
                                                ->label('মোবাইল'),

                                            TextEntry::make('staff.nid_no')
                                                ->label('NID'),

                                        ])
                                        ->columns(3)
                                        ->columnSpanFull(),

                                ])
                                ->collapsible()
                                ->collapsed(),

                            /*
                            |--------------------------------------------------------------------------
                            | অস্ত্রের তথ্য
                            |--------------------------------------------------------------------------
                            */

                            Section::make('অস্ত্রের তথ্য')
                                ->icon('heroicon-o-shield-check')
                                ->schema([

                                    RepeatableEntry::make('arms')
                                        ->label('অস্ত্রের বিস্তারিত তথ্য')
                                        ->schema([

                                            TextEntry::make('arms_category')
                                                ->label('অস্ত্রের ধরন'),

                                            TextEntry::make('arms_reg_number')
                                                ->label('রেজিস্ট্রেশন নম্বর'),

                                            TextEntry::make('validity_date')
                                                ->label('বৈধতার তারিখ')
                                                ->date('d/m/Y'),

                                            TextEntry::make('ammunition_details')
                                                ->label('গোলাবারুদের তথ্য'),

                                            TextEntry::make('issued_from')
                                                ->label('ইস্যুকারী কর্তৃপক্ষ'),

                                        ])
                                        ->columns(5)
                                        ->columnSpanFull(),

                                ])
                                ->collapsible()
                                ->collapsed(),
                            /*
                            |--------------------------------------------------------------------------
                            | Previous Address & Case
                            |--------------------------------------------------------------------------
                            */

                            Section::make('অন্যান্য তথ্য')
                                ->icon('heroicon-o-information-circle')
                                ->columns(2)
                                ->schema([

                                    TextEntry::make('living_period')
                                        ->label('বর্তমান বাসায় বসবাসের সময়')
                                        ->state(
                                            ($occupancy?->start_date ?? 'N/A')
                                            . ' থেকে '
                                            . ($occupancy?->end_date ?? 'N/A')
                                        ),

                                    TextEntry::make('old_rental')
                                        ->label('পূর্বের বাসস্থানের ঠিকানা')
                                        ->state($record->old_rental ?? 'N/A'),

                                    TextEntry::make('old_flat_owner')
                                        ->label('পূর্বের বাড়ির মালিক')
                                        ->state($record->old_flat_owner ?? 'N/A'),

                                    TextEntry::make('current_case')
                                        ->label('মামলা/অপরাধ সংক্রান্ত তথ্য')
                                        ->state($record->current_case ?? 'N/A')
                                        ->columnSpanFull(),

                                ])
                                ->collapsible()
                                ->collapsed(),

                        ];
                    })

                    /*
                    |--------------------------------------------------------------------------
                    | Approve Button
                    |--------------------------------------------------------------------------
                    */

                    ->extraModalFooterActions([

                        Action::make('approve')
                            ->label('অনুমোদন করুন')
                            ->icon('heroicon-o-check-circle')
                            ->color('success')
                            ->requiresConfirmation()
                            ->modalHeading('বসবাসকারী অনুমোদন করুন')
                            ->modalDescription(
                                'আপনি কি এই বসবাসকারীর আবেদন অনুমোদন করতে চান?'
                            )
                            ->modalSubmitActionLabel('হ্যাঁ, অনুমোদন করুন')
                            ->action(function (Tenant $record) {

                                $record->update([
                                    'status' => 'approved',
                                ]);

                                Notification::make()
                                ->title('বসবাসকারী অনুমোদিত')
                                ->body(
                                    "{$record->tenant_name}-এর আবেদন সফলভাবে অনুমোদন করা হয়েছে।"
                                )
                                ->success()
                                ->duration(5000)
                                ->send();

                            }),

                    ]),

            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }

    private function floorOrdinal($floor): string
    {
        return match ((int) $floor) {
            1 => '১ম',
            2 => '২য়',
            3 => '৩য়',
            4 => '৪র্থ',
            5 => '৫ম',
            6 => '৬ষ্ঠ',
            7 => '৭ম',
            8 => '৮ম',
            9 => '৯ম',
            10 => '১০ম',
            11 => '১১তম',
            12 => '১২তম',
            13 => '১৩তম',
            14 => '১৪তম',
            15 => '১৫তম',
            16 => '১৬তম',
            17 => '১৭তম',
            18 => '১৮তম',
            19 => '১৯তম',
            20 => '২০তম',
            default => "{$floor} তলা",
        };
    }
}
