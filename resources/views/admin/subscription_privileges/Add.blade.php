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
                            <h2 class="content-header-title float-left mb-0">Subscription Privileges </h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#">Home</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="#">Subscription Privileges </a>
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
                                    <h4 class="card-title">Subscription Privileges </h4>
                                </div>
                                <div class="card-body">
                                    <form class="form" action="{{ route('subscription_privileges.store') }}" method="post" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                           <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="plan-name">Name *</label>
                                                    <input type="text" id="plan-name" class="form-control" 
                                                        name="name" placeholder="Enter Name" value="{{ old('name') }}" required>
                                                    @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                                                    
                                                </div>
                                            </div>

                                             <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="plan-name">Code *</label>
                                                    <input type="text" id="plan-name" class="form-control" 
                                                        name="code" placeholder="Enter code" value="{{ old('code') }}" required>
                                                    @error('code') <small class="text-danger">{{ $message }}</small> @enderror
                                                </div>
                                            </div>
                                            

                                            <!-- Buttons -->
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary mr-1">Submit</button>
                                                <button type="reset" class="btn btn-outline-secondary">Reset</button>
                                                <a href="{{ route('subscription_privileges.index') }}" class="btn btn-outline-secondary">Cancel</a>
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
