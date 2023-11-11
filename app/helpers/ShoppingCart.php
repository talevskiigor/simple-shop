<?php

namespace App\helpers;

class ShoppingCart
{
    const SHOPPING_CART_ID = 'shoppingCartId';


    public static function formatPriceAsText($price){

        return html_entity_decode(number_format((float)$price,0,',','.') .' <small>oo</small> ден');
    }

}
