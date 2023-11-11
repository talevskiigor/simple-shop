<?php

namespace App\Http\Controllers;

use App\helpers\ShoppingCart;
use App\Models\Product;
use Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Ramsey\Uuid\Uuid;
use View;

class CartController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // tells the view finder to look for `.html` files and run
        // them through the normal PHP `include` process
        View::addExtension('html', 'php');

        $cart = Cart::session(session(ShoppingCart::SHOPPING_CART_ID,Uuid::uuid4()->toString()));
        return view('cart.pay',[
            'cart'=>$cart
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
    public function store(Request $request)
    {


        if(!$request->session()->has(ShoppingCart::SHOPPING_CART_ID)){

            $request->session()->put(ShoppingCart::SHOPPING_CART_ID,Uuid::uuid4()->toString());
        }
        $cartId = session(ShoppingCart::SHOPPING_CART_ID,Uuid::uuid4()->toString());

//        dd($cartId);
        $id = $request->get('productId');
        $product = Product::findOrFail($id);

        Cart::session($cartId)->add([
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'quantity' => 1,
            'attributes' => [],
            'associatedModel' => $product,
        ]);
//        dd(\Cart::session(session(\App\helpers\ShoppingCart::SHOPPING_CART_ID))->getTotalQuantity());
        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
