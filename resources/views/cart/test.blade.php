<form action='https://www.cpay.com.mk/client/Page/default.aspx?xml_id=/mk-MK/.loginToPay/.simple/' method='post'><!-- Payment Portal to Post -->
<input id='AmountToPay' name='AmountToPay' value='1300' type='hidden' /><!-- Amount to pay multiplied by 100 - without decimal and separators, i.e. amount 1,00 will be value 100-->
<input id='PayToMerchant' name='PayToMerchant' value='1000002188' type='hidden' />		<!-- DO NOT CHANGE! Merchant ID - assigned manually for the time being -->
<input id='MerchantName' name='MerchantName' value='FORTEKST DOOEL' type='hidden' /> <!-- DO NOT CHANGE! Merchant name -->
<input id='AmountCurrency' name='AmountCurrency' value='MKD' type='hidden' /><!-- DO NOT CHANGE! Currency of payment -->
<input id='Details1' name='Details1' value='Invoice 1000' type='hidden' /><!--Userfriendly details of payment-->
<input id='Details2' name='Details2' value='123456789' type='hidden' /><!-- Merchant specific details (OrderID - unique for each payment)-->
<input id='PaymentOKURL' size='10' name='PaymentOKURL' value='http://merchantOKurl.com' type='hidden' /><!-- Merchant PaymentSentOK redirect URL-->
<input id='PaymentFailURL' size='10' name='PaymentFailURL' value='http://merchantFailurl.com' type='hidden' /><!-- Merchant PaymentFailed redirect URL-->
<input id='CheckSumHeader' name='CheckSumHeader' value='18AmountToPay,PayToMerchant,MerchantName,AmountCurrency,Details1,Details2,PaymentOKURL,PaymentFailURL,FirstName,LastName,Address,City,Zip,Country,Telephone,Email,OriginalAmount,OriginalCurrency,004010014003012009024026005008015006004009014019003003' type='hidden' />
<!-- CheckSum header should contain :
1. the number of parameters sent in Redirect Code. The number should be represented as a 2-digit field, right-justified, padded left with zeros.
2. a comma-separated list of parameters names. There also have to be a comma after the last parameter name.
3. a list of lenghts of parameters VALUEs. Each lenght should be represented as a 3-digit field, right-justified, padded left with zeros.
NOTE ! In the CheckSumHeader should be listed ONLY parameters which have values,
i.e. if there is no value for some parameter, it should NOT be included in the CheckSumHeader.
And vice versa - if a value is sent for a parameter, which is not listed in the CheckSumHeader, it is considered as a fraud
and an error is raised
NOTE !! It does not matter the order in which parameters are listed in the CheckSumHeader,
it only matters that the VALUES of the parameters in the Input string (for calculating the checksum) are included in the order EXACTLY the same as parameters names in the CheckSumHeader
-->
<input id='CheckSum' name='CheckSum' value='B321D6124429CBACF2C12A21DE3F3004' type='hidden' />
<!-- CheckSum should be generated according to the algorithm explained in the specification.
For the generation of the sample CheckSum here is used the test merchant password - TEST_PASS -->

<input id='FirstName' size='10' name='FirstName' value='Dejan' type='hidden' /><!-- Customer's first name (Optional) -->
<input id='LastName' size='10' name='LastName' value='Pavlovic' type='hidden' /><!-- Customer's last name (Optional) -->
<input id='Address' size='10' name='Address' value='Orce Nikolov 71' type='hidden' /><!-- Customer's billing address (Optional) -->
<input id='City' size='10' name='City' value='Skopje' type='hidden' /><!-- Customer's city (Optional) -->
<input id='Zip' size='10' name='Zip' value='1000' type='hidden' /><!-- Customer's Zip (Optional) -->
<input id='Country' size='10' name='Country' value='Macedonia' type='hidden' /><!-- Customer's country (Optional) -->
<input id='Telephone' size='10' name='Telephone' value='+389 2 125 495' type='hidden' /><!-- Customer's phone number (Optional) -->
<input id='Email' size='10' name='Email' value='filmfest@mol.com.mk' type='hidden' /><!-- Customer's email (Optional) -->
<input id='OriginalAmount' name='OriginalAmount' value='150' type='hidden' /><!-- Original Amount (Optional) -->
<input id='OriginalCurrency' name='OriginalCurrency' value='EUR' type='hidden' /><!-- Original Currency (Optional) -->
<input class='button' value='Pay' type='submit' />
</form>
