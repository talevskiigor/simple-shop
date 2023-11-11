<div class="col-lg-3 mb-3 d-flex align-items-stretch">
    <div class="card">
        <img src="{{ $item->image }}" class="card-img-top" alt="Card Image">
        @if(rand(0,1))
            <span
                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">-10%  </span>
        @endif
        <div class="card-body d-flex flex-column">
            <h6 class="card-title">{{ $item->name }}</h6>
            <p class="card-text mb-4">{{$item->model}}</p>
            <hr>
            <div class="d-flex justify-content-between align-items-center">
                <a href="/product/{{$item->slug}}" class="btn btn-sm btn-outline-success">Купи</a>
                <div  class="text-success position-relative">
                    {{number_format($item->price,0,',','.')}},<small>oo</small> ден

                </div>
                <a href="/product/{{$item->slug}}" class="btn btn-sm btn-outline-primary">Види</a>

            </div>

        </div>

    </div>
</div>
