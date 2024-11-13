@extends('layouts.main')


@section('content')
    <main>

        <div class="row">
            <div class="col sm-7">
                <h1>{{$item->name}} </h1>
            </div>
            <div class="col col-sm-2 text-sm-end">
                <div  class="btn btn-success position-relative">
                    @if($item->discount & $item->quantity > 0)
                    <del>{{number_format($item->price,2,',','.')}}</del>
                    @endif
                    {{number_format($item->getPrice(),0,',','.')}},<small>oo</small> ден
                        @if($item->discount & $item->quantity > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
-{{$item->discount}}%
  </span>
                        @endif

                </div>

            </div>
            <div class="col col-sm-3  text-sm-end">
                <form action="{{url('cart')}}" method="POST">
                    <input type="hidden" name="productId" value="{{$item->id}}">
                    @csrf
                    <button type="submit" class="btn btn-outline-primary btn-lg"><i class="bi bi-cart-plus"></i> Додај во корпа</button>
                </form>
{{--<div class="row" style="list-style-type: none">{!!--}}
{{--//Share::page(url('/product/' . $item->slug))--}}
{{--Share::page('https://forkids.mk/product/' . $item->slug, null, ['title'=>'sss ssksk k'], '<h1>', '</h1>')--}}
{{--    ->facebook();--}}
{{--!!}--}}
{{--</div>--}}
            </div>
        </div>

<div class="row">
    <div class="col col-sm-8 offset-sm-2">

                <div id="carouselExample" class="carousel slide" data-interval="500">
                    @if(!count($item->media->all()))
                        <div class="carousel-inner">
                            <img src="{{  \App\Helpers\Image::get($item->image,768) }}" class="d-block w-100" alt="{{$item->name}}">
                        </div>
                    @else

                    <div class="carousel-indicators bg-info" >
                        @foreach($item->media as $media)
                        <button type="button" data-bs-target="#carouselExample" data-bs-slide-to="{{$loop->index}}"
                                @if($loop->first)
                                class="active"
                                aria-current="true"
                                @endif
                                aria-label="Slide 1"></button>
                        @endforeach
                    </div>
                    <div class="carousel-inner">
                        @foreach($item->media as $media)
                            <div class="carousel-item @if($loop->first) active @endif">
                                <img src="{{  \App\Helpers\Image::get($media->path,768) }}" class="d-block w-100" alt="{{$media->name}}">
                            </div>
                        @endforeach

                    </div>
                    <button class="carousel-control-prev text-info" type="button" data-bs-target="#carouselExample"
                            data-bs-slide="prev">
                        <h1><span class=" text-info" aria-hidden="true"><</span></h1>
                        <span class="visually-hidden">Предходно</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselExample"
                            data-bs-slide="next">
                        <h1><span class=" text-info" aria-hidden="true">></span></h1>
                        <span class="visually-hidden">Next</span>
                    </button>
                    @endif
                </div>

    </div>
</div>
                <p>&nbsp;</p>

                {!! html_entity_decode($item->description) !!}

        <div class="row">
            <div class="col-sm-12"><hr></div>
            <div class="col col-sm-6"></div>
            <div class="col col-sm-6">
                <form action="{{url('cart')}}" method="POST">
                    <input type="hidden" name="productId" value="{{$item->id}}">
                    @csrf
                    <button type="submit" class="btn btn-outline-primary btn-lg"><i class="bi bi-cart-plus"></i> Додај во корпа</button>
                </form>


            </div>
        </div>
    </main>

    <hr>
@endsection
