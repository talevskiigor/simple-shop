<div class="row">
    <div class="col col-sm-12">


        <div class="card">
            <h5 class="card-header">

                <i class="bi bi-cart4"></i> Кошничка</h5>
            <div class="card-body">
{{--                <h5 class="card-title">Special title treatment</h5>--}}
{{--                <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>--}}

                <div class="row">
                    <div class="col col-1">Р.бр</div>
                    <div class="col col-1">Слика</div>
                    <div class="col col-1">Име</div>
                    <div class="col col-1">Кол</div>
                    <div class="col col-1">Цена</div>
                    <div class="col col-1">Попуст</div>
                    <div class="col col-1">Вкупно</div>
                    <div class="col col-1"></div>
                </div>
                @foreach($cart->getContent() as $item)
                <div class="row">
                    <div class="col col-1">{{$loop->index + 1}}</div>
                    <div class="col col-1"><img src="{{  \App\Helpers\Image::get( $item->associatedModel->image,128) }}" style="width: 124px" class="img-fluid"
                                                alt="Responsive image"></div>
                    <div class="col col-1">
                        {{$item->associatedModel->name}}<br>
                        {{$item->associatedModel->model}} <small>({{$item->associatedModel->id}})</small>
                    </div>
                    <div class="col col-1">{{$item->quantity}}</div>
                    <div class="col col-1">Цена</div>
                    <div class="col col-1">Попуст</div>
                    <div class="col col-1">Вкупно</div>
                    <div class="col col-1"></div>
                </div>
                @endforeach
                <table class="table  table-hover">
                    <tbody>


                            <td class="text-end"> {!!  \App\Helpers\ShoppingCart::formatPriceAsText($item->associatedModel->price) !!}</td>
                            <td class="text-end">{{$item->associatedModel->discount}}%</td>
                            <td class="text-end"> {!!  \App\Helpers\ShoppingCart::formatPriceAsText($item->getPriceWithConditions()) !!}</td>
                            <td class="text-end">
                                <form action='{{url('cart/' . $item->id)}}' method='post'>
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" value='Pay' class="btn btn-outline-danger btn-sm">
                                        X
                                    </button>
                                </form>
                            </td>
                            </td>
                        </tr>
                    <tr>
                    <td colspan="4"></td>
                    <td><strong>Вкупно</strong></td>
                    <td class="text-end"><strong> {!!  \App\Helpers\ShoppingCart::formatPriceAsText($cart->getTotal()) !!}</strong></td>
                    <td class="text-end"><strong> {!!  \App\Helpers\ShoppingCart::formatPriceAsText($cart->getTotal() - $cart->getSubTotalWithoutConditions()) !!}</strong></td>
                    <td class="text-end"><strong> {!!  \App\Helpers\ShoppingCart::formatPriceAsText($cart->getSubTotalWithoutConditions()) !!}</strong></td>
                    <td></td>
                    </tr>
                    <tr>
                    <td colspan="4"></td>
                    <td><strong>Вкупно</strong></td>
                    <td class="text-end"><strong> {!!  \App\Helpers\ShoppingCart::formatPriceAsText($cart->getTotal()) !!}</strong></td>
                    <td class="text-end"><strong> {!!  \App\Helpers\ShoppingCart::formatPriceAsText($cart->getTotal() - $cart->getSubTotalWithoutConditions()) !!}</strong></td>
                    <td class="text-end"><strong> {!!  \App\Helpers\ShoppingCart::formatPriceAsText($cart->getSubTotalWithoutConditions()) !!}</strong></td>
                    <tr></tr>
                    </th>


                    </tbody>
                    <tfoot>
                    <th>
                    <td colspan="4"></td>
                    <td><strong>Вкупно</strong></td>
                    <td class="text-end"><strong> {!!  \App\Helpers\ShoppingCart::formatPriceAsText($cart->getTotal()) !!}</strong></td>
                    <td class="text-end"><strong> {!!  \App\Helpers\ShoppingCart::formatPriceAsText($cart->getTotal() - $cart->getSubTotalWithoutConditions()) !!}</strong></td>
                    <td class="text-end"><strong> {!!  \App\Helpers\ShoppingCart::formatPriceAsText($cart->getSubTotalWithoutConditions()) !!}</strong></td>
                    <td></td>
                    </th>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
</div>


