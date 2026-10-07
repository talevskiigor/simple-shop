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

                        @if(!$attempt)
                            <div class="alert alert-warning" role="status">Test environment: payments are disabled.</div>
                        @else
                        @if($attempt->is_test)
                        <div class="alert alert-warning">Пробно плаќање со реална картичка: ќе се наплати <strong>1 денар вкупно</strong>. Реалната вредност и производите се зачувани; нема испорака или промена на залиха.</div>
                        @endif
                        <div class="alert alert-success" role="alert">
                            Ќе бидете пренасочени на страница на банката каде треба да ја извршите уплатата.
                        </div>
                        <div class="row mb-1">
                            <div class="col col-sm-9"></div>
                            <div class="col col-sm-3 d-flex flex-row-reverse">
                                @if(in_array($attempt->status, ['returned_success', 'confirmed']))
                                    <a href="{{ url('/payment/result/'.$attempt->return_token) }}" class="btn btn-outline-primary">Резултат од плаќањето</a>
                                @else
                                <form action='{{ config('payments.url') }}' method='post' accept-charset="UTF-8">
                                    @foreach(app(\App\Services\Payments\Cpay::class)->form($attempt) as $name => $value)
                                        <input id='{{$name}}' name='{{$name}}' value='{{$value}}' type='hidden' />
                                    @endforeach
                                <button type="submit" value='Pay' class="btn btn-outline-primary btn-lg"><i
                                        class="bi bi-caret-right-square"></i> Плати
                                </button>
                                </form>
                                @endif
                            </div>
                        </div>

                        @endif

                    </div>
                </div>


            </div>
        </div>

    @endsection
