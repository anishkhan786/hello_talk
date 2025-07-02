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
                        <h2 class="content-header-title float-left mb-0">LMS Question View</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">LMS Question View</li>
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
                            <h4 class="card-title">LMS Question View</h4>
                            <a href="{{ route('LMSQuestion.add') }}" class="btn btn-info">LMS Question Add</a>
                        </div>

                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Course Name</th>
                                        <th>Category Name</th>
                                        <th>Question Title</th>
                                        <th>Option A</th>
                                        <th>Option B</th>
                                        <th>Option C</th>
                                        <th>Option D</th>
                                        <th>Correct Answer</th>
                                        <th>Marks</th>
                                        <th>Explanation</th>
                                        <th>Is Active</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $index => $item)
                                    <tr>
                                        <td>{{ ++$index }}</td>
                                        <td>{{ $item->course->name }}</td>
                                        <td>{{ $item->category->name }}</td>
                                        <td>{{ $item->question_text }}</td>
                                        <td>{{ $item->option_a }}</td>
                                        <td>{{ $item->option_b }}</td>
                                        <td>{{ $item->option_c }}</td>
                                        <td>{{ $item->option_d }}</td>
                                        <td>@php $answer ='option_'.$item->correct_answer;  @endphp  {{ $item->$answer }}</td>
                                        <td>{{ $item->marks }}</td>
                                        <td>{{ $item->explanation }}</td>
                                        <td>{{ ($item->is_active == 1)?'Active':'InActive' }}</td>
                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-toggle="dropdown">
                                                    <i data-feather="more-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" href="{{ route('LMSQuestion-edit', $item->id) }}">
                                                        <i data-feather="edit-2" class="mr-50"></i>
                                                        <span>Edit</span>
                                                    </a>
                                                    <a class="dropdown-item" href="{{ route('LMSQuestion-destroy', $item->id) }}">
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
                            {{ $data->links('pagination::bootstrap-4') }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
