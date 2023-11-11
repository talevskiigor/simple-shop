@extends('layouts.main')


@section('content')
    <main>

        <div class="row">
            <div class="col sm-10">
                <h1>{{$item->name}} </h1>
            </div>
            <div class="col-sm-2 text-sm-end">
                <button type="button" class="btn btn-success position-relative">
                    {{number_format($item->price,2,',','.')}} ден
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
    -10%
  </span>
                </button>
            </div>
        </div>


                <div id="carouselExample" class="carousel slide">
                    <div class="carousel-indicators">
                        @foreach($item->media as $media)
                        <button type="button" data-bs-target="#carouselExample" data-bs-slide-to="{{$loop->index}}"
                                @if($loop->first)
                                class="active"
                                aria-current="true"
                                @endif
                                aria-label="Slide 1"><slide/button>
                        @endforeach
                    </div>
                    <div class="carousel-inner">
                        @foreach($item->media as $media)
                            <div class="carousel-item @if($loop->first) active @endif">
                                <img src="{{$media->path}}" class="d-block w-100" alt="{{$media->name}}">
                            </div>
                        @endforeach

                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample"
                            data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselExample"
                            data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
                <p>&nbsp;</p>
                {!! html_entity_decode($item->description) !!}

        <div class="row">
            <div class="col-sm-12"><hr></div>
            <div class="col col-sm-6"></div>
            <div class="col col-sm-6">
                <a class="btn btn-success btn-lg"><i class="bi bi-cash-coin"></i> Купи</a>
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
