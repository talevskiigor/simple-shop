@extends('layouts.admin')
@section('js')
<script>
    $( document ).ready(function() {
        console.log("ready!");

        tinymce.init({
            selector: 'textarea',
            plugins: 'ai tinycomments mentions anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount checklist mediaembed casechange export formatpainter pageembed permanentpen footnotes advtemplate advtable advcode editimage tableofcontents mergetags powerpaste tinymcespellchecker autocorrect a11ychecker typography inlinecss',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | align lineheight | tinycomments | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
            tinycomments_mode: 'embedded',
            tinycomments_author: 'Author name',
            mergetags_list: [
                {value: 'First.Name', title: 'First Name'},
                {value: 'Email', title: 'Email'},
            ],
            ai_request: (request, respondWith) => respondWith.string(() => Promise.reject("See docs to implement AI Assistant")),
        });
    });
</script>
@endsection
@section('header_section')

@endsection

@section('content')
    <form class="delete{{$item->id}}"
          action='{{url('/admin/product/' . $item->id)}}' method='post'>
        @csrf
        @method('patch')
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane" type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true">General</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="description-tab" data-bs-toggle="tab" data-bs-target="#description-tab-pane" type="button" role="tab" aria-controls="description-tab-pane" aria-selected="false">Description</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact-tab-pane" type="button" role="tab" aria-controls="contact-tab-pane" aria-selected="false">Contact</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="disabled-tab" data-bs-toggle="tab" data-bs-target="#disabled-tab-pane" type="button" role="tab" aria-controls="disabled-tab-pane" aria-selected="false" disabled>Disabled</button>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0">

            <div class="row">
                <div class="col col-sm-12">
                    <div class="mt-5">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{$item->name}}">
                    </div>

                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="description-tab-pane" role="tabpanel" aria-labelledby="description-tab" tabindex="0">

            <div class="row">
                <div class="col col-sm-12">
<textarea>{!!  html_entity_decode($item->description)!!}</textarea>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="contact-tab-pane" role="tabpanel" aria-labelledby="contact-tab" tabindex="0">...</div>
        <div class="tab-pane fade" id="disabled-tab-pane" role="tabpanel" aria-labelledby="disabled-tab" tabindex="0">...</div>
    </div>

        <div class="row mt-5">
            <div class="col col-sm-12">
                <button type="submit" value='Pay' class="btn btn-sm btn-success"
                        data-bs-toggle="modal" data-bs-target="#deleteModal"
                >
                    <i class="fa-solid fa-floppy-disk"></i> Save
                </button>

            </div>
        </div>
    </form>
@endsection

