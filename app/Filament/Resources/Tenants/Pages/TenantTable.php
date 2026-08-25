<?php

namespace App\Filament\Resources\Tenants\Pages;

use App\Filament\Resources\Tenants\TenantResource;
use App\Models\Area;
use App\Models\Plot;
use App\Models\Tenant;
use Filament\Actions\Action;
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
use Illuminate\Database\Eloquent\Builder;

class TenantTable extends Page implements HasTable,HasForms
{
    use InteractsWithTable,InteractsWithForms;
    protected static string $resource = TenantResource::class;

    protected string $view = 'filament.resources.tenants.pages.tenant-table';

    public function getTitle(): string
    {
        return __('ভাড়াটিয়া');
    }

     public ?int $area_id = null;
     public ?int $plot_id = null;


     public function getFormSchema(): array
    {
        return [
            Grid::make(4)
                ->schema([
                    Select::make('area_id')
                        ->label('এরিয়া')
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
                    ])

        ];
        
    }


    protected function getTableQuery(): Builder
    {
        return Tenant::query()

            ->with([
                'currentAgreement.occupancy.flat.floor.building.plot.area',
            ])

            ->when(
                $this->area_id,
                function (Builder $query) {

                    $query->whereHas(
                        'currentAgreement.occupancy.flat.floor.building.plot',
                        function (Builder $query) {

                            $query->where(
                                'area_id',
                                $this->area_id
                            );
                        }
                    );
                }
            )

            ->when(
                $this->plot_id,
                function (Builder $query) {

                    $query->whereHas(
                        'currentAgreement.occupancy.flat.floor.building.plot',
                        function (Builder $query) {

                            $query->where(
                                'id',
                                $this->plot_id
                            );
                        }
                    );
                }
            );
    }

    protected function getTableColumns(): array
    {
        return [

            TextColumn::make('tenant_name')
                ->label(__('formlabel.tenant_name'))
                ->searchable()
                ->sortable(),

            TextColumn::make('father_name')
                ->label(__('formlabel.father_name'))
                ->searchable(),

            TextColumn::make('mobile')
                ->label(__('formlabel.mobile'))
                ->searchable(),

            TextColumn::make(
                'currentAgreement.occupancy.flat.floor.building.plot.area.area_name'
            )
                ->label('এরিয়া'),

            TextColumn::make(
                'currentAgreement.occupancy.flat.floor.building.plot.plot_no'
            )
                ->label('প্লট'),

            TextColumn::make(
                'currentAgreement.occupancy.flat.floor.building.building_name'
            )
                ->label('ভবন'),

            TextColumn::make(
                'currentAgreement.occupancy.flat.floor.floor_name'
            )
                ->label('ফ্লোর'),

            TextColumn::make(
                'currentAgreement.occupancy.flat.flat_no'
            )
                ->label('ফ্ল্যাট'),

        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('নতুন ভাড়াটিয়া')
                ->icon(Heroicon::Plus)
                ->tooltip('নতুন ভাড়াটিয়া তৈরি করুন'),
                // ->url(fn ($record) => route('filament.resources.tasks.edit', $record)),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            EditAction::make()
                ->label('')
                ->tooltip('সম্পাদনা করুন'),
                // ->url(fn ($record) => route('filament.resources.tasks.edit', $record)),
            Action::make('single_print')
                ->label('')
                ->icon(Heroicon::Printer)
                ->tooltip('রিপোর্ট প্রিন্ট করুন')
                ->color('success')
                ->url(fn ($record) => route('single-tenant.print', [
                        'tenant' => $record->id,
                    ]))
                ->openUrlInNewTab(),
        ];
    }
}
