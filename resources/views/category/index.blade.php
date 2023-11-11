@extends('layouts.main')


@section('content')
    <main>


        <div class="album py-5 bg-body-tertiary">
            <div class="container">

                @foreach($items as $item)
                        @if ($loop->first)
                            <div class="row" style="margin-bottom: 50px">
                        @endif

                                @if($loop->index % 4 == 0)
                            </div>
                                    <div class="row" style="margin-bottom: 20px" >
                                @endif
                        <div class="col col-sm-3">
                            <div class="card shadow-sm">
                                <img src="{{ $item->image }}">
{{--                                <svg class="bd-placeholder-img card-img-top" width="100%" height="225"--}}
{{--                                     xmlns="/image/{{ $item->image }}" role="img" aria-label="Placeholder: Thumbnail"--}}
{{--                                     preserveAspectRatio="xMidYMid slice" focusable="false"><title>Placeholder</title>--}}
{{--                                    <rect width="100%" height="100%" fill="#55595c"/>--}}
{{--                                    <text x="50%" y="50%" fill="#eceeef" dy=".3em">Thumbnail</text>--}}
{{--                                </svg>--}}
                                <div class="card-body">
                                    <p class="card-text">{{$item->name}}</p>
                                    <div class="d-flex justify-content-between align-items-center">

                                            <a href="/product/{{$item->slug}}" class="btn btn-sm btn-outline-primary">Види</a>
                                        <button type="button" class="btn btn-success position-relative">
                                            {{number_format($item->price,2,',','.')}} ден
                                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
    -10%
  </span>
                                        </button>
                                            <a href="/product/{{$item->slug}}" class="btn btn-sm btn-outline-secondary">Купи</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                                @if ($loop->last)
                            </div>
                                @endif
                @endforeach

            </div>
        </div>

    </main>
@endsection
