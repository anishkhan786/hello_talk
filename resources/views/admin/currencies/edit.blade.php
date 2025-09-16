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
                            <h2 class="content-header-title float-left mb-0">Edit Currency</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#">Home</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="#">Edit Currency</a>
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
                                    <h4 class="card-title">Edit Currency</h4>
                                </div>

                                    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
                                <div class="card-body">
                                    <form action="{{ route('currencies.update', $currency->id) }}" method="POST">
                                            @csrf

                                            <div class="row">
                                                <!-- Country Code -->
                                                <div class="col-md-6 mb-3">
                                                    <label for="country_code">Country Code *</label>
                                                    <input type="text" name="country_code" id="country_code"
                                                        class="form-control" value="{{ old('country_code', $currency->country_code) }}" required>
                                                    @error('country_code') <small class="text-danger">{{ $message }}</small> @enderror
                                                </div>

                                                <!-- Currency Name -->
                                                <div class="col-md-6 mb-3">
                                                    <label for="currency_name">Currency Name *</label>
                                                    <input type="text" name="currency_name" id="currency_name"
                                                        class="form-control" value="{{ old('currency_name', $currency->currency_name) }}" required>
                                                    @error('currency_name') <small class="text-danger">{{ $message }}</small> @enderror
                                                </div>

                                                <!-- Currency Code -->
                                                <div class="col-md-6 mb-3">
                                                    <label for="currency_code">Currency Code *</label>
                                                    <input type="text" name="currency_code" id="currency_code"
                                                        class="form-control" value="{{ old('currency_code', $currency->currency_code) }}" required>
                                                    @error('currency_code') <small class="text-danger">{{ $message }}</small> @enderror
                                                </div>

                                                <!-- Symbol -->
                                                <div class="col-md-6 mb-3">
                                                    <label for="symbol">Symbol</label>
                                                    <input type="text" name="symbol" id="symbol"
                                                        class="form-control" value="{{ old('symbol', $currency->symbol) }}">
                                                    @error('symbol') <small class="text-danger">{{ $message }}</small> @enderror
                                                </div>

                                                <!-- Base Price -->
                                                <div class="col-md-6 mb-3">
                                                    <label for="base_price">Base Price *</label>
                                                    <input type="number" step="0.01" name="base_price" id="base_price"
                                                        class="form-control" value="{{ old('base_price', $currency->base_price) }}" required>
                                                    @error('base_price') <small class="text-danger">{{ $message }}</small> @enderror
                                                </div>

                                                <!-- Is Active -->
                                                <div class="col-md-6 mb-3">
                                                    <label for="is_active">Is Active</label>
                                                    <select name="is_active" id="is_active" class="form-control">
                                                        <option value="1" {{ old('is_active', $currency->is_active) == 1 ? 'selected' : '' }}>Yes</option>
                                                        <option value="0" {{ old('is_active', $currency->is_active) == 0 ? 'selected' : '' }}>No</option>
                                                    </select>
                                                    @error('is_active') <small class="text-danger">{{ $message }}</small> @enderror
                                                </div>

                                                <!-- Buttons -->
                                                <div class="col-12 mt-3">
                                                    <button type="submit" class="btn btn-primary">Update</button>
                                                    <a href="{{ route('currencies.index') }}" class="btn btn-secondary">Cancel</a>
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
