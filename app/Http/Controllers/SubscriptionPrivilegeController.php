<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPrivileges;
use Illuminate\Http\Request;

class SubscriptionPrivilegeController extends Controller
{
    public function index()
    {
        $privileges = SubscriptionPrivileges::latest()->paginate(10);
        return view('admin.subscription_privileges.index', compact('privileges'));
    }

    public function create()
    {
        return view('admin.subscription_privileges.add');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50|unique:subscription_privileges,code',
        ]);

        SubscriptionPrivileges::create($request->all());

        return redirect()->route('subscription_privileges.index')
                         ->with('success', 'Privilege created successfully.');
    }

    public function edit(SubscriptionPrivileges $subscriptionPrivilege)
    {
        return view('admin.subscription_privileges.edit', compact('subscriptionPrivilege'));
    }

    public function update(Request $request, SubscriptionPrivileges $subscriptionPrivilege)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50|unique:subscription_privileges,code,' . $subscriptionPrivilege->id,
        ]);

        $subscriptionPrivilege->update($request->all());

        return redirect()->route('subscription_privileges.index')
                         ->with('success', 'Privilege updated successfully.');
    }

    public function destroy(SubscriptionPrivileges $subscriptionPrivilege)
    {
        $subscriptionPrivilege->delete();

        return redirect()->route('subscription_privileges.index')
                         ->with('success', 'Privilege deleted successfully.');
    }
}
