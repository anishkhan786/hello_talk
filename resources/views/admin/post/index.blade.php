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
                        <h2 class="content-header-title float-left mb-0">Posts View</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">Posts View</li>
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
                            <h4 class="card-title">Posts View</h4>
                            <!-- <a href="{{ route('trooper-together.add') }}" class="btn btn-info">Troopers Together Add</a> -->
                        </div>

                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name </th>
                                        <th>Post Type</th>
                                        <th>Location</th>
                                        <th>Content</th>
                                        <th>Caption</th>
                                        <th>Media</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                     @foreach ($data as $index => $post)
                                    <tr>
                                        <td>{{ ++$index }}</td>
                                        <td>{{ $post->user->name }}</td>
                                        <td>{{ ucfirst($post->post_type) }}</td>
                                        <td>{{ $post->location }}</td>
                                        <td>{{ $post->content }}</td>
                                        <td>{{ $post->caption }}</td>
                                        @if ($post->post_type === 'photo' || $post->post_type === 'video')
                                            <th><a target="_blank" href="/storage/{{$post->media_path}}"><img src="/storage/{{$post->media_path}}" alt="" style="width: 50px;"></a></th>
                                        @else
                                        <th>
                                            @if ($post->post_type === 'carousel')
                                                @foreach ($post->media as $index => $media)
                                                    <a target="_blank" href="/storage/{{$media->media_path}}"><img src="/storage/{{$media->media_path}}" alt="" style="width: 50px;"></a>
                                                @endforeach
                                            @endif
                                        </th>
                                        @endif

                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-toggle="dropdown">
                                                    <i data-feather="more-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <!-- <a class="dropdown-item" href="{{ route('trooper-together-edit', $post->id) }}">
                                                        <i data-feather="edit-2" class="mr-50"></i>
                                                        <span>Post View</span>
                                                    </a> -->
                                                    <a class="dropdown-item" href="{{ route('posts-destroy', $post->id) }}">
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
