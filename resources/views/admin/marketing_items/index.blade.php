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
                        <h2 class="content-header-title float-left mb-0">Marketing Tables</h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">Marketing Table</li>
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
                            <h4 class="card-title">Marketing Table</h4>
                            <a href="{{ route('marketing.add') }}" class="btn btn-info">Add Marketing Item</a>
                        </div>

                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>File</th>
                                        <th>Title</th>
                                        <th>URL</th>
                                        <th>Per Click Price</th>
                                        <th>Total Clicks</th>
                                        <th>Total Amount</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $index => $item)
                                    <tr>
                                        <td>{{ ++$index }}</td>

                                        <td>
                                            @if(!empty($item->media_file))
                                                    @if($item->file_type=='2')
                                                        <a href="{{ asset('storage/' . $item->media_file) }}" target="_blank">
                                                            <video width="200" controls>
                                                            <source src="{{ asset('storage/' . $item->media_file) }}" type="video/mp4">
                                                        </video></a>
                                                    @else
                                                     <a href="{{ asset('storage/' . $item->media_file) }}" target="_blank">
                                                        <img src="{{ asset('storage/' . $item->media_file) }}" width="100">
                                                    </a>
                                                    @endif
                                                @endif
                                        </td>

                                        <td>{{ $item->title }}</td>
                                        <td><a href="{{ $item->url }}" target="_blank"> URL</a></td>
                                        <td>₹ {{ $item->price }}</td>
                                        <td>{{ $item->clicks }}</td>
                                        <td>{{ $item->clicks*$item->price }}</td>

                                        <td>{{ $item->status==1?'Active':'InActive' }} </td>
                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-toggle="dropdown">
                                                    <i data-feather="more-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" href="{{ route('marketing-edit', $item->id) }}">
                                                        <i data-feather="edit-2" class="mr-50"></i>
                                                        <span>Edit</span>
                                                    </a>
                                                    <a class="dropdown-item" href="{{ route('marketing-destroy', $item->id) }}">
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
