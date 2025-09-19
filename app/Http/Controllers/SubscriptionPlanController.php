<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class SubscriptionPlanController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::latest()->paginate(10);
        return view('admin.subscription_plans.index', compact('plans'));
    }

    public function create()
    {
        return view('admin.subscription_plans.Add');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'             => 'required|string|max:100',
            'duration_type'    => 'required|in:day,month,year',
            'duration_value'   => 'required|integer|min:1|max:12',
            'price'            => 'required|numeric|min:0',
            'discounted_price' => 'nullable|numeric|min:0|lte:price',
            'status'           => 'required|in:1,2',
        ]);

        SubscriptionPlan::create($request->all());

        return redirect()->route('subscription_plans.index')
                         ->with('success', 'Subscription plan created successfully.');
    }

    public function show(SubscriptionPlan $subscriptionPlan)
    {
        return view('admin.subscription_plans.show', compact('subscriptionPlan'));
    }

    public function edit(SubscriptionPlan $subscriptionPlan)
    {
        return view('admin.subscription_plans.edit', compact('subscriptionPlan'));
    }

    public function update(Request $request, SubscriptionPlan $subscriptionPlan)
    {
        $request->validate([
            'name'             => 'required|string|max:100',
            'duration_type'    => 'required|in:day,month,year',
            'duration_value'   => 'required|integer|min:1|max:12',
            'price'            => 'required|numeric|min:0',
            'discounted_price' => 'nullable|numeric|min:0|lte:price',
            'status'           => 'required|in:1,2',
        ]);

        $subscriptionPlan->update($request->all());
        return redirect()->route('subscription_plans.index')
                     ->with('success', 'Subscription plan updated successfully.');
    }

    public function destroy(SubscriptionPlan $subscriptionPlan)
    {
        $subscriptionPlan->delete();

        return redirect()->route('subscription_plans.index')
                         ->with('success', 'Subscription plan deleted successfully.');
    }
}