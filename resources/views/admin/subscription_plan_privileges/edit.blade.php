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
                            <h2 class="content-header-title float-left mb-0">Plan Privilege</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#">Home</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="#">Plan Privileges</a>
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @include('admin.flash')
            <div class="content-body">
                <section id="multiple-column-form">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Plan Privilege</h4>
                                </div>
                                <div class="card-body">
                                    <form class="form"  action="{{ route('subscription_plan_privileges.update', $subscriptionPlanPrivilege->id) }}"  method="post"  enctype="multipart/form-data">
                                                @csrf

                                                <div class="row">
                                                    
                                                    <!-- Plan -->
                                                    <div class="col-md-6 col-12">
                                                        <div class="form-group">
                                                            <label for="plan">Plan *</label>
                                                            <select id="plan" class="form-control" name="plan_id" required>
                                                                <option value="">-- Select Plan --</option>
                                                                @foreach($plans as $id => $name)
                                                                    <option value="{{ $id }}" 
                                                                        {{ (old('plan_id', $subscriptionPlanPrivilege->plan_id) == $id) ? 'selected' : '' }}>
                                                                        {{ $name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @error('plan_id') <small class="text-danger">{{ $message }}</small> @enderror
                                                        </div>
                                                    </div>

                                                    <!-- Privilege -->
                                                    <div class="col-md-6 col-12">
                                                        <div class="form-group">
                                                            <label for="privilege">Privilege *</label>
                                                            <select id="privilege" class="form-control" name="privilege_id" required>
                                                                <option value="">-- Select Privilege --</option>
                                                                @foreach($privileges as $id => $name)
                                                                    <option value="{{ $id }}" 
                                                                        {{ (old('privilege_id', $subscriptionPlanPrivilege->privilege_id) == $id) ? 'selected' : '' }}>
                                                                        {{ $name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @error('privilege_id') <small class="text-danger">{{ $message }}</small> @enderror
                                                        </div>
                                                    </div>

                                                    <!-- Access Type -->
                                                    <div class="col-md-6 col-12">
                                                        <div class="form-group">
                                                            <label for="access_type">Access Type *</label>
                                                            <select id="access_type" class="form-control" name="access_type" required>
                                                                <option value="">-- Select Type --</option>
                                                                @foreach($accessTypes as $type)
                                                                    <option value="{{ $type }}" 
                                                                        {{ (old('access_type', $subscriptionPlanPrivilege->access_type) == $type) ? 'selected' : '' }}>
                                                                        {{ $type }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @error('access_type') <small class="text-danger">{{ $message }}</small> @enderror
                                                        </div>
                                                    </div>

                                                    <!-- Limit Value -->
                                                    <div class="col-md-6 col-12">
                                                        <div class="form-group">
                                                            <label for="limit_value">Limit Value (if applicable)</label>
                                                            <input type="number" id="limit_value" class="form-control" 
                                                                name="limit_value" placeholder="Enter Limit Value" 
                                                                value="{{ old('limit_value', $subscriptionPlanPrivilege->limit_value) }}">
                                                            @error('limit_value') <small class="text-danger">{{ $message }}</small> @enderror
                                                        </div>
                                                    </div>

                                                    <!-- Buttons -->
                                                    <div class="col-12">
                                                        <button type="submit" class="btn btn-primary mr-1">Update</button>
                                                        <a href="{{ route('subscription_plan_privileges.index') }}" class="btn btn-outline-secondary">Cancel</a>
                                                    </div>
                                                </div>
                                            </form>


                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection
