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
                        <h2 class="content-header-title float-left mb-0">Questions Tables</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">Questions Table</li>
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
                            <h4 class="card-title">Questions Table</h4>
                            <a href="{{ route('questions.add') }}" class="btn btn-info">Create Question</a>
                        </div>
                        <div class="container">
                            @if(session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif

                            @yield('content')
                        </div>

                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                   <tr>
                                        <th>#</th>
                                        <th>Title</th>
                                        <th>Type</th>
                                        <th>Marks</th>
                                        <th>Options</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>

                                 @foreach($questions as $index => $q)
                                    <tr>
                                        <td>{{ ++$index }}</td>
                                        <td>{{ $q->title }}</td>
                                        <td>{{ $q->type }}</td>
                                        <td>{{ $q->marks }}</td>
                                        <td>{{ $q->options->count() }}</td>
                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-toggle="dropdown">
                                                    <i data-feather="more-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" href="{{ route('questions.show', $q) }}">
                                                        <i data-feather="eye" class="mr-50"></i>
                                                        <span>View</span>
                                                    </a>
                                                     <a class="dropdown-item" href="{{ route('questions.edit', $q) }}">
                                                        <i data-feather="edit-2" class="mr-50"></i>
                                                        <span>Edit</span>
                                                    </a>
                                                    <a class="dropdown-item" href="{{ route('questions.destroy', $q) }}">
                                                        <i data-feather="trash" class="mr-50"></i>
                                                        <span>Delete</span>
                                                    </a>
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
                            {{ $questions->links('pagination::bootstrap-4') }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
