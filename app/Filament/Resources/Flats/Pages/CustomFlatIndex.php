<?php

namespace App\Filament\Resources\Flats\Pages;

use App\Filament\Resources\Flats\FlatResource;
use App\Models\Area;
use App\Models\Flat;
use App\Models\Plot;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CustomFlatIndex extends Page implements HasTable,HasForms
{
    use InteractsWithTable,InteractsWithForms;
    protected static string $resource = FlatResource::class;

    protected string $view = 'filament.resources.flats.pages.custom-flat-index';

    protected static ?string $title = 'ফ্ল্যাট তালিকা';

    public ?int $area_id = null;
    public ?int $plot_id = null;
    public ?string $status = null;

    public function mount(): void
    {
        $this->status = request()->query('status') ?? null;
    }


    public function getFormSchema(): array
    {
        return [
            Grid::make(4)
                ->schema([
                    Select::make('area_id')
                        ->label('এরিয়া')
                        ->options(
                            Area::query()
                                ->orderBy('area_name')
                                ->pluck('area_name', 'id')
                        )
                        ->searchable()
                        ->preload()
                        ->live()
                        ->afterStateUpdated(function () {

                            $this->plot_id = null;

                            $this->resetTable();
                        }),


                    Select::make('plot_id')
                        ->label('প্লট')
                        ->options(function () {

                            if (! $this->area_id) {
                                return [];
                            }

                            return Plot::query()
                                ->where('area_id', $this->area_id)
                                ->orderBy('plot_no')
                                ->pluck('plot_no', 'id');
                        })
                        ->searchable()
                        ->preload()
                        ->live()
                        ->disabled(fn () => ! $this->area_id)
                        ->afterStateUpdated(function () {

                            $this->resetTable();
                        }),

                        Select::make('status')
                        ->label('ফ্ল্যাটের অবস্থা')
                        ->options([
                            'all' => 'সকল ফ্ল্যাট',
                            'vacant' => 'খালি ফ্ল্যাট',
                            'occupied' => 'দখলকৃত ফ্ল্যাট',
                        ])
                        ->default('all')
                        ->live()
                        ->afterStateUpdated(function ($state) {
                            $this->resetTable();
                        }),
                    ])

        ];
        
    }


    protected function getTableQuery(): Builder
    {
        return Flat::query() 
        ->with([ 'floor.building.plot.area', 'floor.building.plot.owners.user', 'owners.user', ])
        ->when($this->area_id, function ($query) {
                $query->whereHas('floor.building.plot', function ($query) {
                    $query->where('area_id', $this->area_id);
                });
            })
            ->when($this->plot_id, function ($query) {
                $query->whereHas('floor.building.plot', function ($query) {
                    $query->where('id', $this->plot_id);
                });
            })
            ->when(
            $this->status === 'vacant',
            function (Builder $query) {

                $query->where(function (Builder $query) {

                    // ১. Current occupancy = vacant
                    $query->whereHas('occupancies', function (Builder $query) {
                        $query
                            ->where('occupancy_type', 'vacant')
                            ->where('is_current', true);
                    })

                    // ২. কখনো কোনো occupancy ছিল না
                    ->orWhereDoesntHave('occupancies');

                });
            }
        )

        // দখলকৃত ফ্ল্যাট
        ->when(
            $this->status === 'occupied',
            function (Builder $query) {

                $query->whereHas('occupancies', function (Builder $query) {
                    $query->where('is_current', true)
                        ->whereIn('occupancy_type', [
                            'tenant',
                            'owner',
                        ]);
                });
            }
        );
    }

    protected function getTableColumns(): array
    {
        return[
            TextColumn::make('floor.building.plot.plot_no')
                    ->label(__('formlabel.plot_no'))
                    ->searchable(),
                TextColumn::make('floor.building.plot.area.area_name')
                    ->label(__('formlabel.area_name'))
                    ->searchable(),
                TextColumn::make('owner_name')
                    ->label('বর্তমান মালিক')
                    ->state(function ($record) {

                        // প্রথমে Flat-এর current owner খুঁজবে
                        $flatOwners = $record->owners
                            ->where('is_current', true);

                        if ($flatOwners->isNotEmpty()) {
                            return $flatOwners
                                ->map(fn ($owner) => $owner->user?->name)
                                ->filter()
                                ->implode(', ');
                        }

                        // Flat owner না থাকলে Plot-এর current owner দেখাবে
                        $plotOwners = $record->floor
                            ?->building
                            ?->plot
                            ?->owners
                                ?->where('is_current', true);

                        if ($plotOwners?->isNotEmpty()) {
                            return $plotOwners
                                ->map(fn ($owner) => $owner->user?->name)
                                ->filter()
                                ->implode(', ');
                        }

                        return '-';
                    })
                    ->searchable()
                    ->wrap(),
                // TextColumn::make('floor.building.building_name')
                //     ->label(__('formlabel.building_name'))
                //     ->searchable(),
                TextColumn::make('floor.floor_name')
                    ->label(__('formlabel.floor_name'))
                    ->searchable(),
                TextColumn::make('flat_no')
                    ->label(__('formlabel.flat_no'))
                    ->searchable(),
                TextColumn::make('flat_side')
                    ->label(__('formlabel.flat_side'))
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'North' => 'উত্তর',
                        'South' => 'দক্ষিণ',
                        'East'  => 'পূর্ব',
                        'West'  => 'পশ্চিম',
                        default => $state ?? '-',
                    })
                    ->searchable(),
                TextColumn::make('flat_area')
                    ->label(__('formlabel.flat_area'))
                    ->numeric()
                    ->sortable()
                    ->suffix('  স্কয়ার ফিট'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
        ];
    }
    

    protected function getTableActions(): array
    {
        return [
            EditAction::make()
                ->label('')
                ->icon('heroicon-o-pencil-square')
                ->tooltip('সম্পাদনা করুন')
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make()
            //     ->icon(Heroicon::Plus),
        ];
    }
    
}
