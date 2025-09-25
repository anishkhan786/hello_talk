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
                            <h2 class="content-header-title float-left mb-0">Question Form</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="">Home</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="#">Question Forms</a>
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
                                    <h4 class="card-title">Question Form</h4>
                                </div>
                                <div class="card-body">
                                    <form class="form" action="{{ route('questions.store') }}" method="post" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                             <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label class="first-name-column">Topic</label>
                                                    <select name="topic_id"  class="form-control" required>
                                                        <option value="">--Select Topic--</option>

                                                        @foreach($topic as $t)
                                                        <option value="{{$t->id}}">{{$t->name}}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('topic_id')
                                                        <p class='text-danger text-xs pt-1'> {{ $message }} </p>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="first-name-column">Title</label>
                                                    <input type="text" id="first-name-column" class="form-control"
                                                        placeholder="Title" name="title"  value="{{ old('title') }}" required/>
                                                         @error('title')
                                                        <p class='text-danger text-xs pt-1'> {{ $message }} </p>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-12 col-12">
                                                <div class="form-group">
                                                    <label for="country-floating">Description</label>
                                                  <textarea name="description" class="form-control" >{{ old('description') }}</textarea>
                                                
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="country-floating">Marks</label>
                                                    <input type="number" name="marks" class="form-control" value="1">
                                                     @error('marks')
                                                        <p class='text-danger text-xs pt-1'> {{ $message }} </p>
                                                    @enderror
                                                </div>
                                            </div>

                                             <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label class="first-name-column">Type</label>
                                                    <select name="type" id="qtype" class="form-control" required>
                                                        <option value="mcq">Multiple Choice</option>
                                                        <option value="true_false">True / False</option>
                                                      
                                                        <option value="matching">Matching</option>
                                                    </select>
                                                    @error('type')
                                                        <p class='text-danger text-xs pt-1'> {{ $message }} </p>
                                                    @enderror
                                                </div>
                                            </div>

                                             <div class="col-md-12 col-12" >
                                                <div class="form-group" id="options-area" style="display:none;">
                                                    <label for="country-floating">Options</label>
                                                    <div id="options-list"></div>
                                                    <button type="button" id="add-option" class="btn btn-sm btn-outline-secondary mt-2">Add option</button>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary mr-1">Submit</button>
                                                <button type="reset" class="btn btn-outline-secondary">Reset</button>
                                                 <a href="{{route('questions')}}" class="btn btn-outline-secondary"> Back</a>
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
(function(){
    const qtype = document.getElementById('qtype');
    const optionsArea = document.getElementById('options-area');
    const optionsList = document.getElementById('options-list');
    const addBtn = document.getElementById('add-option');

    function showHide(){
        const t = qtype.value;
        if(t === 'mcq' || t === 'true_false' || t === 'matching'){
            optionsArea.style.display = 'block';
            if(optionsList.children.length === 0){
                // add 2 default options for convenience
                addOption(); addOption();
            }
            // if true_false ensure exactly two options: True and False
            if(t === 'true_false'){
                optionsList.innerHTML = '';
                addOption('True', true);
                addOption('False', false);
            }
        } else {
            optionsArea.style.display = 'none';
            optionsList.innerHTML = '';
        }
    }

    function addOption(text = '', isCorrect = false){
        const idx = optionsList.children.length;
        const wrapper = document.createElement('div');
        wrapper.className = 'input-group mb-2 option-row';
        wrapper.innerHTML = `\
            <input type=\"text\" name=\"options[${idx}][option_text]\" class=\"form-control\" placeholder=\"Option text\" value=\"${text}\">\
            <div class=\"input-group-text\">\
              <label style=\"margin:0 6px 0 0\">Correct</label>\
              <input type=\"checkbox\" name=\"options[${idx}][is_correct]\" ${isCorrect ? 'checked' : ''}>\
               &nbsp;<button type=\"button\" class=\"btn btn-sm btn-outline-danger ms-2 remove-opt\">Remove</button>\
            </div>`;
        optionsList.appendChild(wrapper);

        wrapper.querySelector('.remove-opt').addEventListener('click', function(){
            wrapper.remove();
            // reindex names
            [...optionsList.querySelectorAll('.option-row')].forEach((row, i) => {
                const input = row.querySelector('input[type=text]');
                input.name = `options[${i}][option_text]`;
                const chk = row.querySelector('input[type=checkbox]');
                chk.name = `options[${i}][is_correct]`;
            });
        });
    }

    addBtn.addEventListener('click', function(){ addOption(); });
    qtype.addEventListener('change', showHide);
    // initial
    showHide();
})();
</script>
@endpush