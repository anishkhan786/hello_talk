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
                            <h2 class="content-header-title float-left mb-0">Language Form</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.html">Home</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="#">Language Forms</a>
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
                                    <h4 class="card-title">Language Form</h4>
                                </div>
                                <div class="card-body">
                                    <form class="form" action="{{ route('category-edit.update', $data->id) }}" method="post" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">

                                        <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="first-name-column">Course Name</label>
                                                    <select name="course_id" class="form-control" id=""required>
                                                        @foreach($course  as $val)
                                                            <option value="{{$val->id}}" @if($val->id == $data->course_id) selected @endif>{{$val->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="first-name-column">Category Name</label>
                                                    <input type="text" id="first-name-column" class="form-control"
                                                        placeholder="name" name="name" value="{{$data->name}}" />
                                                </div>
                                            </div>
                                            

                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary mr-1">Submit</button>
                                                 &nbsp;&nbsp;&nbsp;&nbsp;<a href="{{route('category')}}" class="btn btn-outline-secondary"> Back</a>
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
