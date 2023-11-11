<section class="bg-light pt-5 pb-5 shadow-sm">
    <div class="container" style="padding-top: 40px">
        <div class="row">
            @foreach($items as $item)
                @include('_partials.item-in-grid')
            @endforeach

        </div>
    </div>
</section>
