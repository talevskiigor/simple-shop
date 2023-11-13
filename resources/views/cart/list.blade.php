<div class="row">
    <div class="col col-sm-12">


        <div class="card">
            <h5 class="card-header">Featured</h5>
            <div class="card-body">
                <h5 class="card-title">Special title treatment</h5>
                <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                <table class="table">
                    <thead>
                    <tr>
                    <td>No</td>
                    <td>Image</td>
                    <td>Name</td>
                    <td>Model</td>
                    <td>Quantity</td>
                    <td>Price</td>
                    <td>Total</td>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($cart->getContent() as $item)

                        <tr>
                            <td>{{$loop->index + 1}}</td>
                            <td> <img src="{{$item->associatedModel->image}}" style="width: 124px" class="img-fluid" alt="Responsive image"></td>
                            <td>{{$item->associatedModel->name}}</td>
                            <td>{{$item->associatedModel->model}} <small>({{$item->associatedModel->id}})</small></td>
                            <td>{{$item->quantity}}</td>

                            <td> {!!  \App\helpers\ShoppingCart::formatPriceAsText($item->associatedModel->price) !!}
                            <td> {!!  \App\helpers\ShoppingCart::formatPriceAsText($item->getPriceSum()) !!}
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
                        <td><strong>Total</strong></td>
                        <td><strong> {!!  \App\helpers\ShoppingCart::formatPriceAsText($cart->getTotal()) !!}</strong></td>
                    </th>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
</div>


