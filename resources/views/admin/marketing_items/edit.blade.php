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
                            <h2 class="content-header-title float-left mb-0">Marketing Form</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="">Home</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="#">Marketing Forms</a>
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
                                    <h4 class="card-title">Marketing Form</h4>
                                </div>
                                <div class="card-body">
                                    <form class="form" action="{{route('marketing-edit.update',$item->id)}}" method="post" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                             <div class="col-md-12 col-12">
                                                <div class="form-group">
                                                    <label>Title</label>
                                                    <textarea name="title" class="form-control" >{{ old('title', $item->title ?? '') }}</textarea>
                                                     @error('title')
                                                        <p class='text-danger text-xs pt-1'> {{ $message }} </p>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label>File Type</label>
                                                    <select name="file_type" class="form-control">
                                                        <option value="1" {{ old('file_type',$item->file_type ?? '') == 1 ? 'selected' : '' }}>Image</option>
                                                        <option value="2" {{ old('file_type',$item->file_type ?? '') == 2 ? 'selected' : '' }}>Video</option>
                                                    </select>
                                                </div>
                                            </div>

                                             <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                <label>Attachment (Image or MP4 Video)</label>
                                                <input type="file" name="attachment"  class="form-control">
                                                 @error('attachment')
                                                        <p class='text-danger text-xs pt-1'> {{ $message }} </p>
                                                    @enderror
                                                </div>
                                                @if(!empty($item->media_file))
                                                    @if($item->file_type=='2')
                                                        <video width="300" controls>
                                                            <source src="{{ Storage::disk('s3')->url($item->media_file) }}" type="video/mp4">
                                                        </video>
                                                    @else
                                                       
                                                            <img src="{{ Storage::disk('s3')->url($item->media_file) }}" width="300">
                                                       
                                                    @endif
                                                @endif

                                            </div>

                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label>Per Click Price (INR)</label>
                                                    <input type="number" name="price" class="form-control" value="{{ old('price', $item->price ?? '') }}" required>
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label>Total Clicks</label>
                                                    <input type="number" name="total_click" class="form-control" value="{{ old('total_click', $item->clicks ?? '') }}" required>
                                                </div>
                                            </div>
                                             <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label>Click URL</label>
                                                    <input type="url" name="url" class="form-control" value="{{ old('url', $item->url ?? '') }}" required>
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label>Status</label>
                                                    <select name="status" class="form-control">
                                                        <option value="1" {{ old('status', $item->status ?? '') == 1 ? 'selected' : '' }}>Active</option>
                                                        <option value="2" {{ old('status', $item->status ?? '') == 2 ? 'selected' : '' }}>Inactive</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary mr-1">Submit</button>
                                                <a href="{{route('marketing')}}" class="btn btn-outline-secondary"> Back</a>
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
