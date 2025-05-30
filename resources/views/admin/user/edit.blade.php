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
                            <h2 class="content-header-title float-left mb-0">Country Form</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.html">Home</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="#">Country Forms</a>
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
                                    <h4 class="card-title">User Form</h4>
                                </div>
                                <div class="card-body">
                                    <form class="form" action="{{route('user.update')}}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $data->id }}">

                                        <div class="row">
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="name">Name</label>
                                                    <input type="text" id="name" class="form-control" name="name"
                                                        placeholder="Name" value="{{ $data->name }}">
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="email">Email</label>
                                                    <input type="text" id="email" class="form-control" name="email"
                                                        placeholder="Email" value="{{ $data->email }}">
                                                </div>
                                            </div>

                                            {{-- <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="password">Password</label>
                                                    <input type="password" id="password" class="form-control"
                                                        name="password" placeholder="Password"
                                                        value="{{ $data->password }}">
                                                </div>
                                            </div> --}}

                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="bio">Bio</label>
                                                    <input type="text" id="bio" class="form-control" name="bio"
                                                        placeholder="Bio" value="{{ $data->bio }}">
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="native_language">Native Language</label>
                                                    <select id="native_language" class="form-control"
                                                        name="native_language">
                                                        <option value="">Select language</option>
                                                        @foreach ($languages as $lang)
                                                            <option value="{{ $lang->name }}"
                                                                {{ $data->native_language == $lang->name ? 'selected' : '' }}>
                                                                {{ $lang->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="learning_language">Learning Language</label>
                                                    <select id="learning_language" class="form-control"
                                                        name="learning_language">
                                                        <option value="">Select language</option>
                                                        @foreach ($languages as $lang)
                                                            <option value="{{ $lang->name }}"
                                                                {{ $data->learning_language == $lang->name ? 'selected' : '' }}>
                                                                {{ $lang->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>

                                                </div>
                                            </div>

                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="country">Country</label>
                                                    <select id="country" class="form-control" name="country">
                                                        <option value="">Select country</option>
                                                        @foreach ($countries as $country)
                                                            <option value="{{ $country->name }}"
                                                                {{ $data->country == $country->name ? 'selected' : '' }}>
                                                                {{ $country->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>

                                                </div>
                                            </div>

                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="gender">Gender</label>
                                                    <select id="gender" class="form-control" name="gender">
                                                        <option value="">Select gender</option>
                                                        <option value="Male"
                                                            {{ $data->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                                        <option value="Female"
                                                            {{ $data->gender == 'Female' ? 'selected' : '' }}>Female
                                                        </option>
                                                        <option value="Other"
                                                            {{ $data->gender == 'Other' ? 'selected' : '' }}>Other</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="dob">Date of Birth</label>
                                                    <input type="date" id="dob" class="form-control"
                                                        name="dob" value="{{ $data->dob }}">
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="image">Profile Image</label>
                                                    <input type="file" id="image" class="form-control"
                                                        name="image">
                                                    @if ($data->avatar)
                                                        <img src="{{ url('storage/' . $data->avatar) }}"
                                                            alt="Profile Image" height="100" width="100px"
                                                            class="mt-1">
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary mr-1">Submit</button>
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
