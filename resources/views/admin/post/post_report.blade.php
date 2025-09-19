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
                        <h2 class="content-header-title float-left mb-0">Post report View</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">Posts report View</li>
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
                            <h4 class="card-title">Posts report View</h4>
                            <!-- <a href="{{ route('trooper-together.add') }}" class="btn btn-info">Troopers Together Add</a> -->
                        </div>

                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name </th>
                                        <th>Post Type</th>
                                        <th>Post Report</th>
                                        <th>Media</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                     @foreach ($data as $index => $item)
                                    <tr>
                                        <td>{{ ++$index }}</td>
                                        <td>{{ $item->user->name??'NA' }}</td>
                                        <td>{{ ucfirst($item->post->post_type??'') }}</td>
                                        <td>{{ $item->reason }}</td>
                                        
                                      
                                            @if(!empty($item->post->post_type) AND ($item->post->post_type === 'photo' || $item->post->post_type === 'video' ) )
                                                <th><a target="_blank" href="{{ Storage::disk('s3')->url($item->post->media_path) }}">
                                                    <img src="{{ Storage::disk('s3')->url($item->post->media_path) }}" alt="" style="width: 50px;">
                                                </a></th>
                                            @else
                                            <th>
                                                @if (!empty($item->post->post_type) AND $item->post->post_type === 'carousel')
                                                @php 
                                                    $post_media_get = post_media_get($item->post_id);
                                                @endphp
                                                    @foreach ($post_media_get as $index => $media)
                                                        <a target="_blank" href="{{ Storage::disk('s3')->url($media->media_path) }}">
                                                            <img src="{{ Storage::disk('s3')->url($media->media_path) }}" alt="" style="width: 50px;">
                                                        </a>
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
                                                    <a class="dropdown-item" href="{{ route('posts-destroy', $item->post_id) }}">
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
