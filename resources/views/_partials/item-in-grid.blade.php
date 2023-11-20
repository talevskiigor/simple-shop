<div class="col-lg-3 mb-3 d-flex align-items-stretch">
    <div class="card">
        <img src="{{  \App\Helpers\Image::get($item->image,256) }}" class="card-img-top img-thumbnail" style="max-height: 256px" alt="Card Image">
        @if($item->discount && $item->quantity)
            <span
                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">-{{$item->discount}}%  </span>
        @endif
        <div class="card-body d-flex flex-column">
            <h6 class="card-title">{{ $item->name }}</h6>
            <p class="card-text mb-4">{{$item->model}}</p>
            <hr>
            <div class="d-flex justify-content-between align-items-center">

                @if($item->discount & $item->quantity > 0)
                    <div  class="text-muted position-relative">
                        <del><small> {{number_format($item->price,0,',','.')}},<small>oo</small> ден </small></del>
                    </div>
                    <div  class="text-success position-relative">
                        {{number_format($item->price - ($item->price * $item->discount/100),0,',','.')}},<small>oo</small> ден
                    </div>

                @elseif($item->quantity > 0)
                    <div  class="text-success position-relative">
                        {{number_format($item->price,0,',','.')}},<small>oo</small> ден

                    </div>
                @else
                    <div  class="text-muted position-relative">
                        {{number_format($item->price,0,',','.')}},<small>oo</small> ден

                    </div>
                @endif


                @if($item->quantity > 0)
                    <a href="/product/{{$item->slug}}" class=" btn btn-sm btn-outline-primary stretched-link">Види</a>
                @else

                    <div href="#" class="text-danger"
                         rel="tooltip" data-bs-toggle="tooltip" data-bs-title="Јавете се за да проверите дали може да се нарача."
                    >Нема на залиха.</div>
                @endif

            </div>

        </div>

    </div>
</div>
