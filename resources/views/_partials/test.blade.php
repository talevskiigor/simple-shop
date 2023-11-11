<script>
    $('#form').submit(function(e) {
        e.preventDefault();
        var data = {
            subject: $('[name=body]).val(),
            body: $('[name=body]).val()
        };

        $.ajax({
            url: SALES_FORCE_URL,
            method: 'POST',
            beforeSend: function(xhr) {
                //add auth header for SalesForce
                xhr.setRequestHeader("Authorization", "Basic " + btoa('user:pass'));
            },
            data: data,
            success: function() {
                console.log("It worked!")
            }
        }
    })
</script>

<form id="'form" action='https://www.cpay.com.mk/client/Page/default.aspx?xml_id=/mk-MK/.loginToPay/.simple/'
      method='post'>
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
</form>
