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
                            <h2 class="content-header-title float-left mb-0">LMS Question Edit</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.html">Home</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="#">LMS Question Edit</a>
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
                                    <h4 class="card-title">LMS Question Edit</h4>
                                </div>
                                <div class="card-body">
                                    <form class="form" action="{{route('LMSQuestion-edit.update', $data->id)}}" method="post" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="first-name-column">Course</label>
                                                    <select name="course_id"  onchange="courseSelect(this)" class="form-control" id="course_id" required>
                                                        <option value="">--Select Course--</option>
                                                        @foreach($course  as $val)
                                                            <option value="{{$val->id}}" @if($val->id == $data->course_id) selected @endif>{{$val->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                             <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="first-name-column">Category</label>
                                                    <select  id="category_id" name="category_id" class="form-control" id=""required>
                                                       <option value="">--Select Category--</option>
                                                         @foreach($Category  as $val1)
                                                            <option value="{{$val1->id}}" @if($val1->id == $data->category_id) selected @endif>{{$val1->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="first-name-column">Question Title</label>
                                                    <input type="text" id="first-name-column" class="form-control"
                                                        placeholder="Question Title" name="question_text" value="{{$data->question_text}}" required />
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="first-name-column">Marks</label>
                                                    <input type="text" id="first-name-column" class="form-control"
                                                        placeholder="Marks" name="marks" value="{{$data->marks}}" required />
                                                </div>
                                            </div>                                        
                                            <div class="col-md-12 col-12">
                                                <div class="form-group">
                                                    <label for="first-name-column">Explanation</label>
                                                    <textarea type="text" id="first-name-column" class="form-control"
                                                        placeholder="Explanation" name="explanation" required >{{$data->explanation}}</textarea>
                                                </div>
                                            </div>

                                             <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="first-name-column">Option A</label>
                                                    <input type="text" id="first-name-column" class="form-control"
                                                        placeholder="Option A" name="option_a" value="{{$data->option_a}}" required />
                                                </div>
                                            </div>
                                             <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="first-name-column">Option B</label>
                                                    <input type="text" id="first-name-column" class="form-control"
                                                        placeholder="Option B" name="option_b" value="{{$data->option_b}}" required />
                                                </div>
                                            </div>
                                             <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="first-name-column">Option C</label>
                                                    <input type="text" id="first-name-column" class="form-control"
                                                        placeholder="Option C" name="option_c" value="{{$data->option_c}}" required />
                                                </div>
                                            </div>
                                             <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="first-name-column">Option D</label>
                                                    <input type="text" id="first-name-column" class="form-control"
                                                        placeholder="Option D" name="option_d"value="{{$data->option_d}}"  required />
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="first-name-column">Correct Answer</label>
                                                    <select name="correct_answer" class="form-control" id=""required>
                                                        <option value="">--Select--</option>
                                                        <option value="a" @if($data->correct_answer == 'a') selected @endif>Option A</option>
                                                        <option value="b" @if($data->correct_answer == 'b') selected @endif>Option B</option>
                                                        <option value="c" @if($data->correct_answer == 'c') selected @endif>Option C</option>
                                                        <option value="d" @if($data->correct_answer == 'd') selected @endif>Option D</option>
                                                    </select>
                                                </div>
                                                 @error('correct_answer')
                                                <p class='text-danger text-xs pt-1'> {{ $message }} </p>
                                                @enderror
                                            </div>
                                           
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="first-name-column">Is Active</label>
                                                    <select name="is_active" class="form-control" id=""required>
                                                        <option value="1" @if($data->is_active == '1') selected @endif>Active</option>
                                                        <option value="2" @if($data->is_active == '2') selected @endif>InActive</option>
                                                    </select>
                                                </div>
                                            </div>
                                             
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary mr-1">Submit</button>
                                                <a href="{{route('LMSQuestion')}}" class="btn btn-outline-secondary"> Back</a>
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
@push('scripts')
<script>
    function courseSelect(selectElement) {
       var  courseId =  selectElement.value;
         if (courseId) {
                $.ajax({
                    url: '/get-categories-by-course/' + courseId, // your Laravel route
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        let categorySelect = $('select[name="category_id"]');
                        categorySelect.empty(); // remove previous options
                        categorySelect.append('<option value="">--Select Category--</option>');

                        // populate category dropdown
                        $.each(data, function (index, value) {
                            categorySelect.append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    },
                    error: function () {
                        alert("Failed to fetch categories. Please try again.");
                    }
                });
            } else {
                $('select[name="category_id"]').empty().append('<option value="">--Select Category--</option>');
            }
    }
</script>
@endpush