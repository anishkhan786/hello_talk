<?php 
namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use App\Models\SubscriptionPrivileges;
use App\Models\SubscriptionPlanPrivileges;
use Illuminate\Http\Request;

class SubscriptionPlanPrivilegeController extends Controller
{
    public function index()
    {
        $planPrivileges = SubscriptionPlanPrivileges::with(['plan', 'privilege'])->paginate(10);
        return view('admin.subscription_plan_privileges.index', compact('planPrivileges'));
    }

    public function create()
    {
        $plans = SubscriptionPlan::pluck('name','id');
        $privileges = SubscriptionPrivileges::pluck('name','id');
        $accessTypes = ['X', 'Limited', 'Unlimited', 'Maximum'];
        return view('admin.subscription_plan_privileges.Add', compact('plans','privileges','accessTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'privilege_id' => 'required|exists:subscription_privileges,id',
            'access_type' => 'required|in:X,Limited,Unlimited,Maximum',
            'limit_value' => 'nullable|integer|min:1',
        ]);

        SubscriptionPlanPrivileges::create($request->all());

        return redirect()->route('subscription_plan_privileges.index')
                         ->with('success','Plan Privilege created successfully.');
    }

    public function edit(SubscriptionPlanPrivileges $subscriptionPlanPrivilege,$id)
    {
        
        $plans = SubscriptionPlan::pluck('name','id');
        $privileges = SubscriptionPrivileges::pluck('name','id');
        $accessTypes = ['X', 'Limited', 'Unlimited', 'Maximum'];
        $subscriptionPlanPrivilege = SubscriptionPlanPrivileges::find($id);
        return view('admin.subscription_plan_privileges.edit', compact('subscriptionPlanPrivilege','plans','privileges','accessTypes'));
    }

    public function update(Request $request,$id)
    {
        $subscriptionPlanPrivilege = SubscriptionPlanPrivileges::find($id);
        $request->validate([
           'plan_id' => 'required|exists:subscription_plans,id',
            'privilege_id' => 'required|exists:subscription_privileges,id',
            'access_type' => 'required|in:X,Limited,Unlimited,Maximum',
            'limit_value' => 'nullable|integer|min:1',
        ]);

        $subscriptionPlanPrivilege->update($request->all());

        return redirect()->route('subscription_plan_privileges.index')
                         ->with('success','Plan Privilege updated successfully.');
    }

    public function delete($id)
    {
       $data = SubscriptionPlanPrivileges::find($id);
       $data->delete();
        return redirect()->route('subscription_plan_privileges.index')
                         ->with('success','Plan Privilege deleted successfully.');
    }
}
