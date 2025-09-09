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
                            <h2 class="content-header-title float-left mb-0">Question Topic Form</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#">Home</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="#">Question Topic Forms</a>
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
                                    <h4 class="card-title">Question Topic Form</h4>
                                </div>
                                <div class="card-body">
                                    <form class="form" action="{{route('question_topic.store')}}" method="post" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                             <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label class="first-name-column">Language</label>
                                                    <select name="language_id"  class="form-control" required>
                                                        <option value="">--Select Language--</option>

                                                        @foreach($language as $l)
                                                        <option value="{{$l->id}}">{{$l->name}}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('topic_id')
                                                        <p class='text-danger text-xs pt-1'> {{ $message }} </p>
                                                    @enderror
                                                </div>
                                            </div>
                                             <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label class="first-name-column">Learning Level</label>
                                                    <select name="learning_level"  class="form-control" required>
                                                        <option value="">--Select Learning Level--</option>

                                                        @foreach($learning_level as $ll)
                                                        <option value="{{$ll->id}}">{{$ll->name}}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('topic_id')
                                                        <p class='text-danger text-xs pt-1'> {{ $message }} </p>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="first-name-column">Topic Name</label>
                                                    <input type="text" id="first-name-column" class="form-control"
                                                        placeholder="Title" name="name" required />
                                                </div>
                                            </div>


                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="country-floating">Topic Description</label>
                                                    <input type="text" id="country-floating" class="form-control"
                                                        name="description" placeholder="description" required/>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary mr-1">Submit</button>
                                                <button type="reset" class="btn btn-outline-secondary">Reset</button>
                                                 <a href="{{route('question_topic')}}" class="btn btn-outline-secondary"> Back</a>
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
