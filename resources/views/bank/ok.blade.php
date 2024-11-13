@extends('layouts.main')


@section('content')

    <div class="row">
        <div class="col col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col col-sm-1"><h1><i class="bi bi-house"></i></h1></div>
                        <div class="col col-sm-11"><h2> Потврда за нарачка </h2></div>
                    </div>

                </div>
                <div class="card-body">
                    <div class="alert alert-success" role="alert">
                        Вашата уплата е успешна.
                    </div>

                    <h5 class="card-title">Важно !</h5>
                    <p class="card-text">Доставата се врши само до градовите. За други населени места ќе мора да
                        го подигнете производот од магацините во најблискиот град. <br>
                        Очекувајте повик на вашиот телфонски број во наредните 3 до 5 работни дена.
                    </p>
                    <div class="row">
                        <div class="col col-sm-12">
                            <hr>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col col-sm-6">Име</div>
                        <div class="col col-sm-6">{{$order->first}}</div>
                    </div>
                    <div class="row">
                        <div class="col col-sm-6">Презиме</div>
                        <div class="col col-sm-6">{{$order->last}}</div>
                    </div>

                    <div class="row">
                        <div class="col col-sm-6">Адреса</div>
                        <div class="col col-sm-6">{{$order->address}}</div>
                    </div>
                    <div class="row">
                        <div class="col col-sm-6">Град</div>
                        <div class="col col-sm-6">{{$order->city}}</div>
                    </div>
                    <div class="row">
                        <div class="col col-sm-6">Телефон</div>
                        <div class="col col-sm-6">{{$order->phone}}</div>
                    </div>
                    <div class="row">
                        <div class="col col-sm-6">е-маил</div>
                        <div class="col col-sm-6">{{$order->email}}</div>
                    </div>
                    <div class="row">
                        <div class="col col-sm-6">Забелешка</div>
                        <div class="col col-sm-6">{{$order->comment ?? 'n/a'}}</div>
                    </div>
                    <div class="row">
                        <div class="col col-sm-12">
                            <hr>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col col-sm-6">Продукт/и</div>
                        <div class="col col-sm-6">
                            <div class="row">
                                @foreach($items as $item)
                                    <div class="col col-sm-12">{{$item->name}}</div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col col-sm-12">
                            <hr>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col col-sm-6">Вкупно</div>
                        <div
                            class="col col-sm-6 h3"> {!! \App\Helpers\ShoppingCart::formatPriceAsText($order->total)!!}</div>
                    </div>
                    <div class="row">
                        <div class="col col-sm-12">
                            <hr>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>



@endsection
