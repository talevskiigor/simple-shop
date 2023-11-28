@extends('layouts.admin')
@section('js')
    <script>
        $(document).ready(function() {
            console.log("ready!");

            tinymce.init({
                selector: 'textarea',
                plugins: 'ai tinycomments mentions anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount checklist mediaembed casechange export formatpainter pageembed permanentpen footnotes advtemplate advtable advcode editimage tableofcontents mergetags powerpaste tinymcespellchecker autocorrect a11ychecker typography inlinecss',
                toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | align lineheight | tinycomments | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
                tinycomments_mode: 'embedded',
                tinycomments_author: 'Author name',
                //         /* TinyMCE's configuration options */
                // skin: false,
                // content_css: false,

                mergetags_list: [{
                        value: 'First.Name',
                        title: 'First Name'
                    },
                    {
                        value: 'Email',
                        title: 'Email'
                    },
                ],
                ai_request: (request, respondWith) => respondWith.string(() => Promise.reject(
                    "See docs to implement AI Assistant")),
            });
        });
    </script>
@endsection
@section('header_section')
@endsection

@section('content')
    <form class="delete{{ $item->id }} " action='{{ url('/admin/product/' . $item->id) }}' method='post'>
        @csrf
        @method('patch')
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane"
                    type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true">General</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="description-tab" data-bs-toggle="tab" data-bs-target="#description-tab-pane"
                    type="button" role="tab" aria-controls="description-tab-pane"
                    aria-selected="false">Description</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact-tab-pane"
                    type="button" role="tab" aria-controls="contact-tab-pane" aria-selected="false">Contact</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="disabled-tab" data-bs-toggle="tab" data-bs-target="#disabled-tab-pane"
                    type="button" role="tab" aria-controls="disabled-tab-pane" aria-selected="false"
                    disabled>Disabled</button>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab"
                tabindex="0">
                <div class="row">
                    <div class="col col-sm-12">
                        @if ($errors->any())
                            <div class="alert alert-danger mt-5">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif


                    </div>
                </div>
                <div class="row">
                    <div class="col-8 ">
                        <div class="mt-5">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control @if($errors->has('name')) is-invalid @else is-valid @endif" id="name"
                                name="name" value="{{ old('name',$item->name) }}">

                            @if ($errors->has('name'))
                                <div  class="invalid-feedback">
                                    {{ $errors->first('name') }}
                                </div>
                            @endif

                        </div>

                    </div>
                </div>
                <div class="row">
                    <div class="col col-sm-4">
                        <div class="mt-5">
                            <label for="model" class="form-label">Model</label>
                            <input type="text" class="form-control is-invalid" id="model" name="model"
                                value="{{ $item->model }}">
                            <div class="valid-feedback">
                                Looks good!
                            </div>
                            <div id="validationServer05Feedback" class="invalid-feedback">
                                Please provide a valid zip.
                            </div>

                        </div>

                    </div>

                    <div class="col col-sm-4">
                        <div class="mt-5">
                            <label for="price" class="form-label">Price</label>
                            <input type="number" class="form-control" id="price" name="price"
                                value="{{ old('price', $item->price) }}">
                        </div>

                    </div>
                    <div class="col col-sm-4">
                        <div class="mt-5">
                            <label for="quantity" class="form-label">Quantity</label>
                            <input type="number" class="form-control" id="quantity" name="quantity"
                                value="{{ old('quantity', $item->quantity) }}">
                        </div>

                    </div>
                    <div class="col col-sm-4">

                        <label for="discount" class="form-label">Discount</label>
                        <div class="input-group has-validation">
                            <input type="number" class="form-control" id="discount" name="discount"
                                aria-describedby="validationServer05Feedback" required
                                value="{{ old('discount', $item->discount) }}">
                            <div id="validationServer05Feedback" class="invalid-feedback">
                                Please provide a valid zip.
                            </div>
                        </div>

                    </div>

                </div>
            </div>
            <div class="tab-pane fade" id="description-tab-pane" role="tabpanel" aria-labelledby="description-tab"
                tabindex="0">

                <div class="row">
                    <div class="col col-sm-12">
                        <textarea>{!! html_entity_decode($item->description) !!}</textarea>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="contact-tab-pane" role="tabpanel" aria-labelledby="contact-tab"
                tabindex="0">...</div>
            <div class="tab-pane fade" id="disabled-tab-pane" role="tabpanel" aria-labelledby="disabled-tab"
                tabindex="0">...</div>
        </div>

        <div class="row mt-5">
            <div class="col col-sm-12">
                <button type="submit" value='Pay' class="btn btn-sm btn-success" data-bs-toggle="modal"
                    data-bs-target="#deleteModal">
                    <i class="fa-solid fa-floppy-disk"></i> Save
                </button>

            </div>
        </div>


    </form>
@endsection
