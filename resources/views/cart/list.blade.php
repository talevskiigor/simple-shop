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
                            <td>{{$item->associatedModel->model}}</td>
                            <td>{{$item->quantity}}</td>

                            <td> {!!  \App\helpers\ShoppingCart::formatPriceAsText($item->associatedModel->price) !!}
                            <td> {!!  \App\helpers\ShoppingCart::formatPriceAsText($item->getPriceSum()) !!}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>Total</td>
                        <td> {!!  \App\helpers\ShoppingCart::formatPriceAsText($cart->getTotal()) !!}
                    </tr>
                    </tfoot>
                </table>
<div class="row">
    <div class="col col-sm-10"></div>
    <div class="col col-sm-2 d-flex flex-row-reverse">

            <form action='https://www.cpay.com.mk/client/Page/default.aspx?xml_id=/mk-MK/.loginToPay/.simple/' method='post'>
                <input id='AmountToPay' name='AmountToPay' value='1300' type='hidden' />
                <input id='PayToMerchant' name='PayToMerchant' value='1000002188' type='hidden' />
                <input id='MerchantName' name='MerchantName' value='FORTEKST DOOEL' type='hidden' />
                <input id='AmountCurrency' name='AmountCurrency' value='MKD' type='hidden' />
                <input id='Details1' name='Details1' value='Invoice 1000' type='hidden' />
                <input id='Details2' name='Details2' value='123456789' type='hidden' />
                <input id='PaymentOKURL' size='10' name='PaymentOKURL' value='http://merchantOKurl.com' type='hidden' />
                <input id='PaymentFailURL' size='10' name='PaymentFailURL' value='http://merchantFailurl.com' type='hidden' />
                <input id='CheckSumHeader' name='CheckSumHeader' value='18AmountToPay,PayToMerchant,MerchantName,AmountCurrency,Details1,Details2,PaymentOKURL,PaymentFailURL,FirstName,LastName,Address,City,Zip,Country,Telephone,Email,OriginalAmount,OriginalCurrency,004010014003012009024026005008015006004009014019003003' type='hidden' />
                <input id='CheckSum' name='CheckSum' value='B321D6124429CBACF2C12A21DE3F3004' type='hidden' />
                <input class='button' value='Pay' type='submit' />
{{--                <button type="submit" value="""pay" class="btn btn-outline-danger btn-lg"><i class="bi bi-cash-coin"></i> Плати</button>--}}
        </form>
    </div>
</div>
            </div>
        </div>

    </div>
</div>


