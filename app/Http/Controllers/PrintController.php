<?php

namespace App\Http\Controllers;

use App\Models\Flat;
use App\Models\Plot;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class PrintController extends Controller
{
    public function PrintSingleTenant(Request $request)
    {
        $id=$request->query('tenant');
         $tenant = Tenant::with([
        'rentalAgreements',
        'currentAgreement.occupancy.flat.floor.building.plot.area',
        'currentAgreement.occupancy.flat.currentOwners.user',
        'tenantFamilyMembers',
        'vechicles','currentAgreement.profession',
        'rentalAgreements.driverAssignments.staff',
        'housemaidAssignments.staff',
    ])->findOrFail($id);

    // dd($tenant);
        return view('printer.single_tenant_print',compact('tenant'));
    }


    public function PrintTenantList(Request $request)
    {
        $area_id=$request->query('area');
        $plot_id=$request->query('plot');
        $type=$request->query('type');
        $records=Tenant::query()

            ->with([
                'currentAgreement.occupancy.flat.floor.building.plot.area',
            ])

            ->when($area_id, function (Builder $query) use ($area_id) {

                    $query->whereHas('currentAgreement.occupancy.flat.floor.building.plot',
                        function (Builder $query) use ($area_id) {

                            $query->where(
                                'area_id',
                                $area_id
                            );
                        }
                    );
                }
            )

            ->when( $plot_id,function (Builder $query) use ($plot_id) {

                    $query->whereHas('currentAgreement.occupancy.flat.floor.building.plot',
                        function (Builder $query) use ($plot_id) {

                            $query->where(
                                'id',
                                $plot_id
                            );
                        }
                    );
                }
            )
            ->get();

            $totalPlots = Plot::query()->when($area_id, function (Builder $query) use ($area_id) {
                    $query->where('area_id', $area_id);
                })
                ->when($plot_id, function (Builder $query) use ($plot_id) {
                    $query->where('id', $plot_id);
                })->count();

            $totalFlats = Flat::query()->whereHas('floor.building.plot', function (Builder $query) use ($area_id, $plot_id) {

                    $query->when($area_id, function (Builder $query) use ($area_id) {
                            $query->where('area_id', $area_id);
                        })
                        ->when($plot_id, function (Builder $query) use ($plot_id) {
                            $query->where('id', $plot_id);
                        });
                }
            )->count();

            $tenantFlats = Flat::query()->whereHas( 'floor.building.plot', function (Builder $query) use ($area_id, $plot_id) {

                        $query ->when($area_id, function (Builder $query) use ($area_id) {
                                $query->where('area_id', $area_id);
                            })
                            ->when($plot_id, function (Builder $query) use ($plot_id) {
                                $query->where('id', $plot_id);
                            });
                    }
                )->whereHas('occupancies', function (Builder $query) {
                    $query->where('occupancy_type', 'tenant') ->where('is_current', true);
                })->count();
            
            $ownerFlats = Flat::query()

                ->whereHas('floor.building.plot', function (Builder $query) use ($area_id, $plot_id) {

                        $query->when($area_id, function (Builder $query) use ($area_id) {
                                $query->where('area_id', $area_id);
                            })
                            ->when($plot_id, function (Builder $query) use ($plot_id) {
                                $query->where('id', $plot_id);
                            });
                    }
                )->whereHas('occupancies', function (Builder $query) {
                    $query->where('occupancy_type', 'owner')->where('is_current', true);
                })->count();

            // dd($records);
        
        return view($type=='details' ? 'printer.list_tenant_details_print' : 'printer.tenant_list_print', compact('records','totalPlots','totalFlats','tenantFlats','ownerFlats'));
    }
}
