@extends('master')

@section('contant')
<div class="app-content content ">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Subscription Plan</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">Subscription Plan</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('admin.flash')

        <!-- @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif -->
         <div class="content-body">
            <div class="row" id="basic-table">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Plan #{{ $subscriptionPlan->id }}</h4>
                        </div>
                        <div class="card-body">

                            <!-- Plan Details -->
                            <div class="mb-3">
                                <strong>Name:</strong> {{ $subscriptionPlan->name }} <br>
                                <strong>Duration:</strong> {{ $subscriptionPlan->duration_value }} {{ ucfirst($subscriptionPlan->duration_type) }} <br>
                                <strong>Price:</strong> ₹{{ number_format($subscriptionPlan->price, 2) }} <br>
                                <strong>Discounted Price:</strong> ₹{{ number_format($subscriptionPlan->discounted_price, 2) }} <br>
                                <strong>Status:</strong>
                                @if($subscriptionPlan->status == 1)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-danger">Inactive</span>
                                @endif
                            </div>

                            <!-- Privileges -->
                            @if($subscriptionPlan->privileges->count())
                                <div class="mb-3">
                                    <strong>Privileges:</strong>
                                    <ul>
                                        @foreach($subscriptionPlan->privileges as $privilege)
                                            <li>
                                                {{ $privilege->privilege->name ?? 'N/A' }}  
                                                ({{ $privilege->access_type }})
                                                @if($privilege->limit_value)  
                                                    - Limit: {{ $privilege->limit_value }}
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @else
                                <div class="mb-3">
                                    <strong>Privileges:</strong> No privileges assigned.
                                </div>
                            @endif

                            <!-- Buttons -->
                            <a href="{{ route('subscription_plans.index') }}" class="btn btn-secondary">Back</a>
                            <a href="{{ route('subscription_plans.edit', $subscriptionPlan->id) }}" class="btn btn-primary">Edit</a>
                            <form action="{{ route('subscription_plans.destroy', $subscriptionPlan->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure want to delete this plan?')">Delete</button>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
