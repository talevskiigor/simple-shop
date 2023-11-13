<div class="row">
    <div class="col col-sm-12">


        <div class="card">
            <h5 class="card-header">

                <i class="bi bi-cart4"></i> Кошничка</h5>
            <div class="card-body">
{{--                <h5 class="card-title">Special title treatment</h5>--}}
{{--                <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>--}}
                <table class="table  table-hover">
                    <thead>
                    <tr>
                        <td>Р.Бр.</td>
                        <td>Слика</td>
                        <td>Име</td>
                        <td>Модел</td>
                        <td>Количина</td>
                        <td>Цена</td>
                        <td>Вкупно</td>
                        <td></td>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($cart->getContent() as $item)

                        <tr>
                            <td>{{$loop->index + 1}}</td>
                            <td><img src="{{$item->associatedModel->image}}" style="width: 124px" class="img-fluid"
                                     alt="Responsive image"></td>
                            <td>{{$item->associatedModel->name}}</td>
                            <td>{{$item->associatedModel->model}} <small>({{$item->associatedModel->id}})</small></td>
                            <td>{{$item->quantity}}</td>

                            <td> {!!  \App\Helpers\ShoppingCart::formatPriceAsText($item->associatedModel->price) !!}
                            <td> {!!  \App\Helpers\ShoppingCart::formatPriceAsText($item->getPriceSum()) !!}
                            <td>

                            </td>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <th>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><strong>Вкупно</strong></td>
                    <td><strong> {!!  \App\Helpers\ShoppingCart::formatPriceAsText($cart->getTotal()) !!}</strong></td>
                    </th>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
</div>


