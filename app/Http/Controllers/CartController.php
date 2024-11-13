<?php

namespace App\Http\Controllers;

use App\Helpers\ShoppingCart;
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
        $cartId = session(ShoppingCart::SHOPPING_CART_ID, Uuid::uuid4()->toString());
        $cart = Cart::session($cartId);


        foreach ($cart->getContent() as $item) {

            $product = Product::find($item->id);

            if ($product->quantity <= 0) {
                $cart->update($item->id, [
                    'quantity' => [
                        'relative' => false,
                        'value' => 0
                    ],
                ]);
            }
        }

        return view('cart', [
            'cart' => $cart,
            'cartId' => $cartId,
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


        if (!$request->session()->has(ShoppingCart::SHOPPING_CART_ID)) {

            $request->session()->put(ShoppingCart::SHOPPING_CART_ID, Uuid::uuid4()->toString());
        }
        $cartId = session(ShoppingCart::SHOPPING_CART_ID, Uuid::uuid4()->toString());

        $id = $request->get('productId');
        $product = Product::findOrFail($id);

        if ($product->quantity > 0) {
            $item = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
                'attributes' => [],
                'associatedModel' => $product,
            ];

            if ($product->discount) {
                // Let's create first our condition instance
                $saleCondition = new \Darryldecode\Cart\CartCondition(array(
                    'name' => 'SALE 15%',
                    'type' => 'sale',
                    'value' => '-15%',
                ));
                $item['conditions'] = $saleCondition;
            }


            $cart = Cart::session($cartId)->add($item);
            // prevent to always have one item
            $cart->update($product->id, [
                'quantity' => [
                    'relative' => false,
                    'value' => 1
                ],
            ]);
        }
        return redirect('cart');
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
        $cartId = session(ShoppingCart::SHOPPING_CART_ID, Uuid::uuid4()->toString());
        $cart = Cart::session($cartId);
        $cart->remove($id);
        return redirect('cart');
    }
}
