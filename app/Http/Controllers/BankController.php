<?php

namespace App\Http\Controllers;

use App\Helpers\ShoppingCart;
use App\Models\Order;
use App\Models\Product;
use Cart;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class BankController extends Controller
{
    public function ok(Request $request)
    {

        $ref = $request->get('cPayPaymentRef');
        $order = Order::find($request->get('Details2'));
        $order->bank_ref = $ref;
        $order->finished=1;
        $order->save();
        $items = [];
        foreach (json_decode($order->items) as $item){
            $items[] = $item;
            if($product = Product::find($item->id)){
                $product->quantity = $product->quantity - 1;
                $product->save();
            }
        }


        return view('bank.ok',[
            'order'=>$order,
            'items'=>$items,
            ]);


        dd($request->all());
    }
    public function fail(Request $request)
    {
        $order = Order::find($request->get('Details2'));
        $uuid = Uuid::uuid4()->toString();
        if(!$request->session()->has(ShoppingCart::SHOPPING_CART_ID)){
            $request->session()->put(ShoppingCart::SHOPPING_CART_ID, $uuid);
        }
        $cartId = session(ShoppingCart::SHOPPING_CART_ID, $uuid);
        $items = json_decode($order->items);
        foreach ($items as $item){
            $product = Product::find($item->associatedModel->id);
            Cart::session($cartId)->add([
                'id' => $item->id,
                'name' => $item->name,
                'price' => $item->price,
                'quantity' => 1,
                'attributes' => [],
                'associatedModel' => $product,
            ]);

        }

        return view('bank.fail',[

        ]);

    }

}
