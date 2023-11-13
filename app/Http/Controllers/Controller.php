<?php

namespace App\Http\Controllers;

use App\Helpers\ShoppingCart;
use App\Models\Category;
use Cart;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Ramsey\Uuid\Uuid;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    public function __construct(Request $request){



        View::share('cartCount', 0);
        View::share('categories', Category::all());
    }
}
