@extends('layouts.main')

@section('content')

    @include('cart.list')

    <div class="row pt-5">
        <div class="col col-sm-9"></div>
        <div class="col col-sm-3 d-flex flex-row-reverse">

                <a a href="{{url('/order')}}" class="btn btn-success btn-lg"><i class="bi bi-cart-plus"></i> Нарачај</a>
        </div>
    </div>

@endsection
