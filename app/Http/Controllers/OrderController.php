<?php

namespace App\Http\Controllers;

use App\Helpers\ShoppingCart;
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

        $cartId = session(ShoppingCart::SHOPPING_CART_ID, Uuid::uuid4()->toString());
        $cart = Cart::session($cartId);
        if($cart->isEmpty()){
            return redirect('cart');
        }
        $order = Order::findOrNew(request()->session()->get(ShoppingCart::ORDER_ID));

        $first = request()->old('first') ?? $order->first;
        $last = request()->old('last') ?? $order->last;
        $address = request()->old('address') ?? $order->address;
        $city = request()->old('city') ?? $order->city;
        $phone = request()->old('phone') ?? $order->phone;
        $email = request()->old('email') ?? $order->email;
        $comment = request()->old('comment') ?? $order->comment;


        return view('order.index', [
            'cart' => $cart,
            'first' => $first,
            'last' => $last,
            'address' => $address,
            'city' => $city,
            'phone' => $phone,
            'email' => $email,
            'comment' => $comment,
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

        if ($cart->isEmpty()) {
            return redirect('/cart');
        }
        $data = $request->validated();
        $data['items'] = $cart->getContent()->toJson();
        $data['total'] = $cart->getTotal();
        $order = Order::where('finished', false)->find($request->session()->get(ShoppingCart::ORDER_ID));
        $order = $order ?? new Order;
        $order->fill($data)->save();
        $request->session()->put(ShoppingCart::ORDER_ID, $order->id);

        return view('order.confirm', [
            'cart' => $cart,
            'order' => $order
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
