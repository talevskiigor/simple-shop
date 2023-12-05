@extends('layouts.admin')

@section('header_section')

@endsection


@section('content')

    <div class="row">

        @foreach($files as $file)
            <div  class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3" >
                <div class="card h-100" >
                    <div class="card-header">
                        {!! dump($file) !!}
                       <small> {{$file->getBasename()}}</small>
                    </div>
                    <div class="card-body text-center ">
                        <img src="{!! \Img::get($file->getPathname(), \Img::W256,\Img::W256)!!}" class="img-thumbnail" alt="...">



                    </div>
                    <div class="card-footer bg-white ">
                        <p class="card-text "></p>
                    </div>
                </div>
            </div>
        @endforeach


    </div>
@endsection
