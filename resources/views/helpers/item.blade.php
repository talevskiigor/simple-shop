<!-- Gallery item -->
<div class="col-xl-3 col-lg-4 col-md-6 mb-4">
    <div class="bg-white rounded shadow-sm">
        <img src="{{  \App\Helpers\Image::get($item->image,346) }}" alt="" class="img-fluid card-img-top">
        @if($item->discount && $item->quantity)
            <span
                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">-{{$item->discount}}%  </span>
        @endif

        <div class="p-4">
            <h5> <a href="" class="text-dark">{{ $item->name }}...</a></h5>
            <p class="small text-muted mb-0">{{$item->model}}</p>
            <div class="d-flex align-items-center justify-content-between rounded-pill bg-light px-3 py-2 mt-4">
                <p class="small mb-0"><i class="fa fa-picture-o mr-2"></i><span class="font-weight-bold">JPG</span></p>
                <div class="badge badge-danger px-3 rounded-pill font-weight-normal">New</div>
            </div>
        </div>
    </div>
</div>
<!-- End -->
