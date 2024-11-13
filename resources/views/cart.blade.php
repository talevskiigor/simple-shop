@extends('layouts.main')

@section('content')
    {{--    @if(\Cart::session(session(\App\Helpers\ShoppingCart::SHOPPING_CART_ID,Ramsey\Uuid\Uuid::uuid4()->toString()))->isEmpty())--}}
    @if($cart->isEmpty())

        <div class="row">
            <div class="col col-sm-8 offset-sm-2 text-center">
                <div class="alert alert-info">
                    <h1><i class="bi bi-exclamation-circle-fill"></i></h1>
                    <h3>Немате избрано продукти.</h3>
                </div>

            </div>
        </div>
    @else

        <div class="row">
            <div class="col col-sm-12">


                <div class="card">
                    <h5 class="card-header">

                        <i class="bi bi-cart4"></i> Кошничка</h5>
                    <div class="card-body">
                        <table class="table  table-hover">
                            <thead>
                            <tr>
                                {{--                        <th scope="col">#</th>--}}
                                <th scope="col">Слика</th>
                                <th scope="col">Име</th>
                                <th scope="col">Кол.</th>
                                {{--                        <th scope="col" class="text-end">Цена</th>--}}
                                <th scope="col" class="text-end">Попуст</th>
                                <th scope="col" class="text-end">Вкупно</th>
                                <th scope="col"></th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($cart->getContent() as $item)

                                <tr>
                                    @if($item->quantity<=0)
                                        class="table-danger"
                                        rel="tooltip" data-bs-toggle="tooltip" data-bs-title="Избраниот продукт не е
                                        веќе на
                                        залиха. Јавете се за да проверите дали може да се нарача."
                                    @endif
                                    >
                                    {{--                            <th scope="row">{{$loop->index + 1}}</th>--}}
                                    <td><img src="{{  \App\Helpers\Image::get( $item->associatedModel->image,128) }}"
                                             style="width: 124px" class="img-fluid"
                                             alt="Responsive image"></td>
                                    <td>
                                        {{$item->associatedModel->name}}<br>
                                        {{$item->associatedModel->model}} <small>({{$item->associatedModel->id}}
                                            )</small>
                                    </td>
                                    <td>{{$item->quantity}}</td>

                                    {{--                            <td class="text-end"> {!!  \App\Helpers\ShoppingCart::formatPriceAsText($item->associatedModel->price) !!}</td>--}}
                                    <td class="text-end">-{{$item->associatedModel->discount}}%</td>
                                    <td class="text-end"> {!!  \App\Helpers\ShoppingCart::formatPriceAsText($item->getPriceSum()) !!}</td>
                                    <td class="text-end">
                                        <form action='{{url('cart/' . $item->id)}}' method='post'>
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" value='Pay' class="btn btn-outline-danger btn-sm">
                                                X
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="3" class="text-end">Вкупно</td>
                                <td colspan="2"
                                    class="text-end text-danger"> {!!  \App\Helpers\ShoppingCart::formatPriceAsText($cart->getSubTotalWithoutConditions()) !!}</td>
                            </tr>

                            <tr>
                                <td colspan="3" class="text-end">Попуст</td>
                                <td colspan="2"
                                    class="text-end text-primary"> {!!  \App\Helpers\ShoppingCart::formatPriceAsText($cart->getTotal() - $cart->getSubTotalWithoutConditions()) !!}</td>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-end">За наплата</th>
                                <th colspan="2"
                                    class="text-end text-success"> {!!  \App\Helpers\ShoppingCart::formatPriceAsText($cart->getTotal()) !!}</th>
                            </tr>


                            </tfoot>
                        </table>
                    </div>
                </div>

            </div>
        </div>




        <div class="row pt-5">
            <div class="col col-sm-9"></div>
            <div class="col col-sm-3 d-flex flex-row-reverse">

                <a a href="{{url('/order')}}" class="btn btn-success btn-lg"><i class="bi bi-cart-plus"></i> Нарачај</a>
            </div>
        </div>
    @endif
@endsection
