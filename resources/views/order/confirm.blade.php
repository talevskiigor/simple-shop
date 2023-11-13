@extends('layouts.main')

@section('content')

    @extends('layouts.main')

    @section('content')

        <div class="row">
            <div class="col col-sm-8 offset-sm-2">

                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col col-sm-1"><h1><i class="bi bi-house"></i></h1></div>
                            <div class="col col-sm-11"><h2> Потврда на податоци </h2></div>
                        </div>

                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Достава е бесплатна *</h5>
                        <p class="card-text">Доставата се врши само до градовите.<br> За други населени места ќе мора да
                            го подигнете производот од магацините во најблискиот град.</p>
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
                                    @foreach($cart->getContent() as $item)
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
                                class="col col-sm-6 h3"> {!! \App\Helpers\ShoppingCart::formatPriceAsText($cart->getTotal())!!}</div>
                        </div>
                        <div class="row">
                            <div class="col col-sm-12">
                                <hr>
                            </div>
                        </div>

                        <div class="alert alert-success" role="alert">
                            Ќе бидете пренасочени на страница на банката каде треба да ја извршите уплатата.
                        </div>
                        @include('order.pay-button')
                        <div class="row mb-1">
                            <div class="col col-sm-9"></div>
                            <div class="col col-sm-3 d-flex flex-row-reverse">
                                <form action='https://www.cpay.com.mk/client/Page/default.aspx?xml_id=/mk-MK/.loginToPay/.simple/' method='post'>
                                    @foreach(\App\Classes\CaSys::get($order) as $name => $value)
                                        <input id='{{$name}}' name='{{$name}}' value='{{$value}}' type='text' />
                                    @endforeach
                                <button type="submit" value='Pay' class="btn btn-outline-primary btn-lg"><i
                                        class="bi bi-caret-right-square"></i> Плати
                                </button>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>


            </div>
        </div>

    @endsection

@endsection
