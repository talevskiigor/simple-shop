<?php

namespace App\Http\Controllers;

use App\helpers\ShoppingCart;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use Cart;
use Ramsey\Uuid\Uuid;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $first = request()->old('first');
        $last = request()->old('last');
        $address = request()->old('address');
        $city = request()->old('city');
        $phone = request()->old('phone');
        $email = request()->old('email');
        $comment = request()->old('comment');
//        dd($phone);
        $cartId = session(ShoppingCart::SHOPPING_CART_ID, Uuid::uuid4()->toString());
        $cart = Cart::session($cartId);

        return view('order.index',[
            'cart'=>$cart,
            'first'=>$first,
            'last'=>$last,
            'address'=>$address,
            'city'=>$city,
            'phone'=>$phone,
            'email'=>$email,
            'comment'=>$comment,
        ]);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {

        $cartId = session(ShoppingCart::SHOPPING_CART_ID, Uuid::uuid4()->toString());
        $cart = Cart::session($cartId);

        return view('order.index',[
            'cart'=>$cart,
            'phone'=>'071'
        ]);

    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }
}
