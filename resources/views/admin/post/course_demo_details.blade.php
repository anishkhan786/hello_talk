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
                        <h2 class="content-header-title float-left mb-0">Course Details View</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">Course Details View</li>
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
                            <h4 class="card-title">Course Details View</h4>
                            <!-- <a href="{{ route('trooper-together.add') }}" class="btn btn-info">Troopers Together Add</a> -->
                        </div>

                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User Name </th>
                                        <th>Email</th>
                                        <th>Mobile Number</th>
                                        <th>DOB</th>
                                        <th>Learn Language</th>
                                        <th>Learning Level</th>
                                        <th>Country</th>
                                    </tr>
                                </thead>
                                <tbody>
                                     @foreach ($data as $index => $course)
                                    <tr>
                                        <td>{{ ++$index }}</td>
                                        <td>{{ $course->user->name??'NA' }}</td>
                                        <td>{{ $course->email }}</td>
                                        <td>{{ $course->mobile_number }}</td>
                                        <td>{{ $course->dob }}</td>
                                        <td>{{ $course->language->name }}</td>
                                        <td>{{ $course->learningLevel->name }}</td>
                                        <td>{{ $course->country->name }}</td>

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
