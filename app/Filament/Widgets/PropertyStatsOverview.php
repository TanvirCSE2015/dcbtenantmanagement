<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Areas\AreaResource;
use App\Filament\Resources\Flats\FlatResource;
use App\Filament\Resources\Plots\PlotResource;
use App\Filament\Resources\Tenants\TenantResource;
use App\Models\Area;
use App\Models\Flat;
use App\Models\Occupancy;
use App\Models\Plot;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PropertyStatsOverview extends StatsOverviewWidget
{
    protected static bool $isLazy = false;
    protected function getStats(): array
    {
        /*
        |--------------------------------------------------------------------------
        | Total Plots
        |--------------------------------------------------------------------------
        */

        $totalAreas = Area::count();

        /*
        |--------------------------------------------------------------------------
        | Total Plots
        |--------------------------------------------------------------------------
        */
        $totalPlots = Plot::count();


        /*
        |--------------------------------------------------------------------------
        | Total Flats
        |--------------------------------------------------------------------------
        */
        $totalFlats = Flat::count();


        /*
        |--------------------------------------------------------------------------
        | Current Tenants
        |--------------------------------------------------------------------------
        |
        | occupancy_type = tenant
        | এবং is_current = true
        |
        */
        $currentTenants = Occupancy::query()
            ->where('occupancy_type', 'tenant')
            ->where('is_current', true)
            ->count();


        /*
        |--------------------------------------------------------------------------
        | Current Owner Occupancy
        |--------------------------------------------------------------------------
        |
        | occupancy_type = owner
        | এবং is_current = true
        |
        */
        $currentOwnerOccupancy = Occupancy::query()
            ->where('occupancy_type', 'owner')
            ->where('is_current', true)
            ->count();


        /*
        |--------------------------------------------------------------------------
        | Current Vacant Flats
        |--------------------------------------------------------------------------
        |
        | যেসব Flat-এর কোনো current occupancy নেই
        |
        */
        $vacantFlats = Flat::query()
            ->whereDoesntHave('occupancies', function ($query) {
                $query->where('is_current', true);
            })
            ->count();


        return [

            Stat::make(
                    'মোট এলাকা',
                    $this->en2bn(number_format($totalAreas))
                )
                ->description('ক্যান্টনমেন্ট বোর্ড আওতাধীন এলাকা')
                ->descriptionIcon('heroicon-m-map-pin')
                ->color('white')
                ->extraAttributes([
                    'class' => 'property-stat stat-area',
                ])
                ->url(AreaResource::getUrl('index')),


            // 2. মোট প্লট
            Stat::make(
                    'মোট প্লট',
                    $this->en2bn(number_format($totalPlots))
                )
                ->description('সকল নিবন্ধিত প্লট')
                ->descriptionIcon('heroicon-m-map')
                ->color('white')
                ->extraAttributes([
                    'class' => 'property-stat stat-plot',
                ])
                ->url(PlotResource::getUrl('index')),


            // 3. মোট ফ্ল্যাট
            Stat::make(
                    'মোট ফ্ল্যাট',
                    $this->en2bn(number_format($totalFlats))
                )
                ->description('সকল নিবন্ধিত ফ্ল্যাট')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('white')
                ->extraAttributes([
                    'class' => 'property-stat stat-flat',
                ])
                ->url(FlatResource::getUrl('index')),



            // 4. বর্তমান ভাড়াটিয়া
            Stat::make(
                    'বর্তমান ভাড়াটিয়া',
                    $this->en2bn(number_format($currentTenants))
                )
                ->description('বর্তমানে ভাড়ায় থাকা ফ্ল্যাট')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('white')
                ->extraAttributes([
                    'class' => 'property-stat stat-tenant',
                ])
                ->url(TenantResource::getUrl('index',['type' => 'tenant'])),


            // 5. বর্তমান নিজ বসতি
            Stat::make(
                    'বর্তমান নিজ বসতি',
                    $this->en2bn(number_format($currentOwnerOccupancy))
                )
                ->description('মালিক নিজে বসবাস করছেন')
                ->descriptionIcon('heroicon-m-home')
                ->color('white')
                ->extraAttributes([
                    'class' => 'property-stat stat-owner',
                ])
                ->url(TenantResource::getUrl('index',['type' => 'owner'])),


            // 6. খালি ফ্ল্যাট
            Stat::make(
                    'খালি ফ্ল্যাট',
                    $this->en2bn(number_format($vacantFlats))
                )
                ->description('বর্তমানে কোনো বসবাসকারি নেই')
                ->descriptionIcon('heroicon-m-home-modern')
                ->color('white')
                ->extraAttributes([
                    'class' => 'property-stat stat-vacant',
                ])
                ->url(FlatResource::getUrl('index',['status' => 'vacant'])),

        ];
    }

    public function en2bn($number): string
    {
        $en = ['0','1','2','3','4','5','6','7','8','9','January','February','March','April','May','June','July','August','September','October','November','December'];
        $bn = ['০','১','২','৩','৪','৫','৬','৭','৮','৯','জানুয়ারি','ফেব্রুয়ারি','মার্চ','এপ্রিল','মে','জুন','জুলাই','আগস্ট','সেপ্টেম্বর','অক্টোবর','নভেম্বর','ডিসেম্বর'];
        return str_replace($en, $bn, $number);
    }
}
