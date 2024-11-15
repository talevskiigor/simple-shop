@extends('layouts.main')


@section('content')

    <div class="container-fluid">
    <div class="px-lg-5">

        <!-- For demo purpose -->
{{--        <div class="row py-5">--}}
{{--            <div class="col-lg-12 mx-auto">--}}
{{--                <div class="text-white p-5 shadow-sm rounded banner">--}}
{{--                    <h1 class="display-4">Bootstrap 4 photo gallery</h1>--}}
{{--                    <p class="lead">Bootstrap photogallery snippet.</p>--}}
{{--                    <p class="lead">Snippet by <a href="https://bootstrapious.com/snippets" class="text-reset">--}}
{{--                            Bootstrapious</a>, Images by <a href="https://unsplash.com" class="text-reset">Unsplash</a>.--}}
{{--                    </p>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
        <!-- End -->

        <div class="row">
            @foreach($items as $item)
                @include('helpers.item')
            @endforeach

            <!-- Gallery item -->
{{--            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">--}}
{{--                <div class="bg-white rounded shadow-sm"><img src="https://bootstrapious.com/i/snippets/sn-gallery/img-1.jpg" alt="" class="img-fluid card-img-top">--}}
{{--                    <div class="p-4">--}}
{{--                        <h5> <a href="#" class="text-dark">Red paint cup</a></h5>--}}
{{--                        <p class="small text-muted mb-0">Lorem ipsum dolor sit amet, consectetur adipisicing elit</p>--}}
{{--                        <div class="d-flex align-items-center justify-content-between rounded-pill bg-light px-3 py-2 mt-4">--}}
{{--                            <p class="small mb-0"><i class="fa fa-picture-o mr-2"></i><span class="font-weight-bold">JPG</span></p>--}}
{{--                            <div class="badge badge-danger px-3 rounded-pill font-weight-normal">New</div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
            <!-- End -->



        </div>
{{--        <div class="py-5 text-right"><a href="#" class="btn btn-dark px-5 py-3 text-uppercase">Show me more</a></div>--}}
    </div>
</div>
@endsection
