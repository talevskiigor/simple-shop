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
                        <td>Кол.</td>
                        <td class="text-end">Цена</td>
                        <td class="text-end">Вкупно</td>
                        <td></td>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($cart->getContent() as $item)

                        <tr
                            @if($item->quantity<=0)
                            class="table-danger"
                            rel="tooltip" data-bs-toggle="tooltip" data-bs-title="Избраниот продукт не е веќе на залиха. Јавете се за да проверите дали може да се нарача."
                            @endif
                        >
                            <td>{{$loop->index + 1}}</td>
                            <td><img src="{{  \App\Helpers\Image::get( $item->associatedModel->image,128) }}" style="width: 124px" class="img-fluid"
                                     alt="Responsive image"></td>
                            <td>
                                {{$item->associatedModel->name}}<br>
                            {{$item->associatedModel->model}} <small>({{$item->associatedModel->id}})</small>
                            </td>
                            <td>{{$item->quantity}}</td>

                            <td class="text-end"> {!!  \App\Helpers\ShoppingCart::formatPriceAsText($item->associatedModel->price) !!}
                            <td class="text-end"> {!!  \App\Helpers\ShoppingCart::formatPriceAsText($item->getPriceSum()) !!}
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


