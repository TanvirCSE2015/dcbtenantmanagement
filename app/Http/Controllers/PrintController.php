<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
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
        'vechicles',
        'driverAssignments.staff',
        'housemaidAssignments.staff',
    ])->findOrFail($id);

    // dd($tenant);
        return view('printer.single_tenant_print',compact('tenant'));
    }
}
