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
                            <h2 class="content-header-title float-left mb-0">Troopers Together Edit</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.html">Home</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="#">Troopers Together Edit</a>
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
                                    <h4 class="card-title">Troopers Together Edit</h4>
                                </div>
                                <div class="card-body">
                                    <form class="form" action="{{route('trooper-together-edit.update', $data->id)}}" method="post" enctype="multipart/form-data">
                                        @csrf
                                         <div class="row">
                                            <div class="col-md-12 col-12">
                                                <div class="form-group">
                                                    <label for="first-name-column">Title</label>
                                                    <input type="text" id="first-name-column" class="form-control"
                                                        placeholder="Title" name="group_title" value="{{$data->group_title}}" required />
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-12">
                                                <div class="form-group">
                                                    <label for="first-name-column">Description</label>
                                                    <textarea type="text" id="first-name-column" class="form-control"
                                                        placeholder="Description" name="group_description" required >{{$data->group_description}}</textarea>
                                                </div>
                                            </div>
                                             <div class="col-12">
                                                <button type="submit" class="btn btn-primary mr-1">Submit</button>
                                                <a href="{{route('trooper-together')}}" class="btn btn-outline-secondary"> Back</a>
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
