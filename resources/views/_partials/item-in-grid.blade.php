<div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 mb-3">
    <div class="card h-100">
        <div class="card-header">
            <p class="card-text">{{$item->model}}</p>
        </div>
        <img src="{{  \App\Helpers\Image::get($item->image,256) }}" class="card-img-top img-thumbnail" alt="Card Image">
        @if($item->discount && $item->quantity)
            <span
                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">-{{$item->discount}}%  </span>
        @endif
        <div class="card-body">
            <h6 class="card-title">{{ $item->name }}</h6>

        </div>
        <div class="card-footer">

            <div class="row">

                @if($item->discount & $item->quantity > 0)
                    <div class="col-9">
                        <span
                            class="text-muted text-decoration-line-through fs-6 "> {{number_format($item->price,0,',','.')}}</span>

                        <span class="text-success fs-5"> {{number_format($item->price - ($item->price * $item->discount/100),0,',','.')}},<small
                                class="fs-6">oo</small> ден</span>
                    </div>
                @elseif($item->quantity > 0)
                    <div class="col-9">
                        <div class="text-success position-relative">
                            {{number_format($item->price,0,',','.')}},<small>oo</small> ден

                        </div>

                    </div>
                @endif


                @if($item->quantity > 0)
                    <div class="col-2 text-end">
                        <a href="/product/{{$item->slug}}"
                           class=" btn btn-sm btn-outline-primary stretched-link">Види</a>
                    </div>
                @else
                    <div class="col-12 align-middle">
                        <div class="text-danger"
                             rel="tooltip" data-bs-toggle="tooltip"
                             data-bs-title="Јавете се за да проверите дали може да се нарача."
                        >Нема на залиха.
                        </div>
                    </div>
                @endif


            </div>


        </div>

    </div>
</div>
