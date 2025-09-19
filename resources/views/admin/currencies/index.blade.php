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
                        <h2 class="content-header-title float-left mb-0">Currency</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">Currency</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('admin.flash')

        <div class="content-body">
            <div class="row" id="basic-table">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Currency</h4>
                            <a href="{{ route('currencies.create') }}" class="btn btn-info">Add Currency</a>
                        </div>

                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Country Code</th>
                                        <th>Country Name</th>
                                        <th>Code</th>
                                        <th>Symbol</th>
                                        <th>Base Price</th>
                                        <th>Active</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($currencies as $index => $currency)
                                    <tr>
                                        <td>{{  ++$index }}</td>
                                        <td>{{ $currency->country_code }}</td>
                                        <td>{{ $currency->currency_name }}</td>
                                        <td>{{ $currency->currency_code }}</td>
                                        <td>{{ $currency->symbol }}</td>
                                        <td>{{ $currency->base_price }}</td>
                                        <td>{{ $currency->is_active ? 'Yes' : 'No' }}</td>

                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-toggle="dropdown">
                                                    <i data-feather="more-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" href="{{ route('currencies.edit', $currency->id) }}">
                                                        <i data-feather="edit-2" class="mr-50"></i>
                                                        <span>Edit</span>
                                                    </a>

                                                     <form action="{{ route('currencies.destroy', $currency->id) }}" method="POST" style="display:inline;">
                                                        @csrf @method('DELETE')
                                                        <button class="dropdown-item" onclick="return confirm('Delete this currency ?')">Delete</button>
                                                    </form>

                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Laravel Pagination -->
                        <div class="d-flex justify-content-end p-2">
                            {{ $currencies->links('pagination::bootstrap-4') }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
