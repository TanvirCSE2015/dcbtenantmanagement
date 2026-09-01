<?php

namespace App\Http\Controllers;

use App\Models\Flat;
use App\Models\OwnershipTransfer;
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
        $flat_id=$request->query('flat');
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
            ->when( $flat_id,function (Builder $query) use ($flat_id) {

                    $query->whereHas('currentAgreement.occupancy.flat',
                        function (Builder $query) use ($flat_id) {

                            $query->where(
                                'id',
                                $flat_id
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

    public function PrintOwnerSummery(Request $request)
    {
        $flatId = $request->query('flat');

        abort_unless($flatId, 404, 'Flat ID পাওয়া যায়নি।'
        );


        $flat = Flat::query()->with(['floor.building.plot.area','currentOwners.user',])->findOrFail($flatId);


        $transfers = OwnershipTransfer::query()

            ->whereMorphedTo('ownable', $flat)

            ->with([
                'items' => function ($query) {
                    $query
                        ->orderByRaw("
                            CASE
                                WHEN direction = 'from' THEN 1
                                WHEN direction = 'to' THEN 2
                                ELSE 3
                            END
                        ");
                },

                'items.owner',
                'items.owner.user',

                'createdBy',
            ])

            ->orderBy('transfer_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $transferTypes = [
            'purchase'    => 'ক্রয়',
            'inheritance' => 'উত্তরাধিকার',
            'gift'        => 'দান',
            'transfer'    => 'হস্তান্তর',
            'other'       => 'অন্যান্য',
        ];

        return view(
            'printer.print-owner-summary',
            compact(
                'flat',
                'transfers',
                'transferTypes'
            )
        );
        
    }
}
