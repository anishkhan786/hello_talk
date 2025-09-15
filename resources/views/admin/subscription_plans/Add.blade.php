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
                                    <li class="breadcrumb-item"><a href="#">Home</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="#">Subscription Plan</a>
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
                                    <h4 class="card-title">Subscription Plan</h4>
                                </div>
                                <div class="card-body">
                                    <form class="form" action="{{ route('subscription_plans.store') }}" method="post" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            
                                            <!-- Plan Name -->
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="plan-name">Plan Name</label>
                                                    <input type="text" id="plan-name" class="form-control" 
                                                        name="name" placeholder="Enter Plan Name" value="{{ old('name') }}" required>
                                                    @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                                                </div>
                                            </div>

                                            <!-- Duration Type -->
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="duration-type">Duration Type</label>
                                                    <select id="duration-type" class="form-control" name="duration_type" required>
                                                        <option value="">-- Select --</option>
                                                        <option value="day" {{ old('duration_type')=='day' ? 'selected' : '' }}>Day</option>
                                                        <option value="month" {{ old('duration_type')=='month' ? 'selected' : '' }}>Month</option>
                                                        <option value="year" {{ old('duration_type')=='year' ? 'selected' : '' }}>Year</option>
                                                    </select>
                                                    @error('duration_type') <small class="text-danger">{{ $message }}</small> @enderror
                                                </div>
                                            </div>

                                            <!-- Duration Value -->
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="duration-value">Duration Value</label>
                                                    <input type="number" id="duration-value" class="form-control" 
                                                        name="duration_value" placeholder="e.g. 1,2,3..." value="{{ old('duration_value') }}" required>
                                                    @error('duration_value') <small class="text-danger">{{ $message }}</small> @enderror
                                                </div>
                                            </div>

                                            <!-- Price -->
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="price">Price</label>
                                                    <input type="number" step="0.01" id="price" class="form-control" 
                                                        name="price" placeholder="Enter Price" value="{{ old('price') }}" required>
                                                    @error('price') <small class="text-danger">{{ $message }}</small> @enderror
                                                </div>
                                            </div>

                                            <!-- Discounted Price -->
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="discounted-price">Discounted Price</label>
                                                    <input type="number" step="0.01" id="discounted-price" class="form-control" 
                                                        name="discounted_price" placeholder="Enter Discounted Price" value="{{ old('discounted_price') }}">
                                                    @error('discounted_price') <small class="text-danger">{{ $message }}</small> @enderror
                                                </div>
                                            </div>

                                            <!-- Status -->
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="status">Status</label>
                                                    <select id="status" class="form-control" name="status" required>
                                                        <option value="1" {{ old('status')==1 ? 'selected' : '' }}>Active</option>
                                                        <option value="2" {{ old('status')==2 ? 'selected' : '' }}>Inactive</option>
                                                    </select>
                                                    @error('status') <small class="text-danger">{{ $message }}</small> @enderror
                                                </div>
                                            </div>

                                            <!-- Buttons -->
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary mr-1">Submit</button>
                                                <button type="reset" class="btn btn-outline-secondary">Reset</button>
                                                <a href="{{ route('subscription_plans.index') }}" class="btn btn-outline-secondary">Cancel</a>
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
