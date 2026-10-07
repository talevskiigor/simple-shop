<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        return view('cart', ['cart' => Cart::session()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate(['productId' => ['required', 'integer']]);
        $product = Product::findOrFail($data['productId']);
        Cart::session()->add(['id' => $product->id]);
        return redirect('/cart');
    }

    public function destroy(string $id)
    {
        Cart::session()->remove($id);
        return redirect('/cart');
    }
}
