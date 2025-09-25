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
                        <h2 class="content-header-title float-left mb-0">Question Topics Table</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">Question Topics Table</li>
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

                        {{-- Header --}}
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Question Topics Table</h4>
                            <a href="{{ route('question_topic.add') }}" class="btn btn-info">Add Question Topic</a>
                        </div>

                        {{-- Filters --}}
                        <div class="card-body">
                            <form method="GET" action="{{ route('question_topic') }}">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label>Name</label>
                                        <input type="text" name="name" value="{{ request('name') }}" class="form-control" placeholder="Search by name">
                                    </div>

                                    <div class="col-md-3">
                                        <label>Description</label>
                                        <input type="text" name="description" value="{{ request('description') }}" class="form-control" placeholder="Search by description">
                                    </div>

                                    <div class="col-md-3">
                                        <label>Language</label>
                                        <select name="language_id" class="form-control">
                                            <option value="">All</option>
                                            @foreach($languages as $lang)
                                                <option value="{{ $lang->id }}" {{ request('language_id') == $lang->id ? 'selected' : '' }}>
                                                    {{ $lang->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label>Learning Level</label>
                                        <select name="learning_level_id" class="form-control">
                                            <option value="">All</option>
                                            @foreach($levels as $level)
                                                <option value="{{ $level->id }}" {{ request('learning_level_id') == $level->id ? 'selected' : '' }}>
                                                    {{ $level->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="mt-2">
                                    <button type="submit" class="btn btn-primary">Search</button>
                                    <a href="{{ route('question_topic') }}" class="btn btn-secondary">Reset</a>
                                </div>
                            </form>
                        </div>

                        {{-- Table --}}
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Language</th>
                                        <th>Learning Level</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($data as $index => $item)
                                        <tr>
                                            <td>{{ $data->firstItem() + $index }}</td>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->description }}</td>
                                            <td>{{ $item->language->name ?? '' }}</td>
                                            <td>{{ $item->learninglevel->name ?? '' }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-toggle="dropdown">
                                                        <i data-feather="more-vertical"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item" href="{{ route('question_topic-edit', $item->id) }}">
                                                            <i data-feather="edit-2" class="mr-50"></i>
                                                            <span>Edit</span>
                                                        </a>
                                                        <a class="dropdown-item" href="{{ route('question-topic-delete', $item->id) }}">
                                                            <i data-feather="trash" class="mr-50"></i>
                                                            <span>Delete</span>
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No records found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div class="d-flex justify-content-end p-2">
                            {{ $data->appends(request()->all())->links('pagination::bootstrap-4') }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
