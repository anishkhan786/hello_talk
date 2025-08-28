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
                        <h2 class="content-header-title float-left mb-0">Questions Show </h2>
                        <div class="breadcrumb-wrapper">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">Questions show</li>
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
                            <h4 class="card-title">Questions Show</h4>
                        </div>
                         <div class="card-body">
                            <h4>Question #{{ $question->id }}</h4>

                            <div class="mb-3">
                                <strong>Title:</strong>
                                <div>{{ $question->title }}</div>
                                <br>
                                <strong>Type:</strong> {{ $question->type }}
                            </div>
                            @if($question->options->count())
                                <div class="mb-3">
                                    <strong>Options:</strong>
                                    <ul>
                                        @foreach($question->options as $opt)
                                            <li>{!! e($opt->option_text) !!} @if($opt->is_correct) <span class="badge bg-success">Correct</span> @endif</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <a href="{{ route('questions') }}" class="btn btn-secondary">Back</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
