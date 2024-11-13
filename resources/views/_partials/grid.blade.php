<section class="bg-light pt-5 pb-5 shadow-sm">
    <div class="container" style="padding-top: 40px">
        <div class="row">
            @if(count($items))
            @foreach($items as $item)
                @include('_partials.item-in-grid')
            @endforeach
            @else
                <div class="row">
                    <div class="col col-sm-8 offset-sm-2 text-center">
                        <div class="alert alert-info">
                            <h1><i class="bi bi-exclamation-circle-fill"></i></h1>
                            <h3>Нема резултати.</h3>
                        </div>
                    </div>
                </div>

            @endif
        </div>
    </div>
</section>
