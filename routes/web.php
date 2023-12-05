<?php

use App\Http\Controllers\PageController;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::group(['middleware' => ['web']], function () {

    Route::get('/', function () {

        $items = Product::all();

        return view('home', [
            'items' => $items
        ]);

    });

    Route::get('/categories/{slug?}', function (string|null $slug) {

        $category = Category::with('product')->where('slug', $slug)->first();
        if(!$category){
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
        }
        $items = $category->product;

        return view('home', [
            'items' => $items
        ]);

    });

    Route::get('/product/{slug?}', function (string|null $slug) {
        $item = Product::where('slug', $slug)->with('media')->with('category')->first();
        return view('product', [
            'item' => $item
        ]);

    });

    Route::get('search', function () {
        $needed =  \request()->get('find');

        $items = Product::search(Str::ascii($needed))->get();
        return view('home', [
            'items' => $items
        ]);

    });

    Route::get('/page/{slug?}', function (string|null $slug) {
        $page = \App\Models\Page::where('slug', $slug)->first();
        return view('helpers.page', ['page' => $page]);

    });
    Route::resource('/order', \App\Http\Controllers\OrderController::class);
    Route::resource('/cart', \App\Http\Controllers\CartController::class);



    Route::resource('/contact', \App\Http\Controllers\ContactController::class);


    Route::get('admin', function () {
        return redirect(route('dashboard'));
    });

    Route::get('update', function () {
        $cmd = 'db:seed --class=OCSeeder --force';
        \Illuminate\Support\Facades\Artisan::call($cmd);
        return \Carbon\Carbon::now()->toString();
    });


    Route::get('/media-resize/{slug?}', function (?string $slug = null) {
        $w = request('w', 'null');
        $h = request('h', 'null');
        $q = request('q', 100);

        $file = public_path('media/' . $slug);
        $originFile = $file;
        $newExtension = 'webp';

        $name = basename($file, '.' . explode('.', $file)[1],);
        $newFile = sprintf('%s/%s-%sX_%s_%s.%s', dirname($file), $name, $w, $h, $q, $newExtension);
        $newFile = str_replace('/var/www/html/public/media', '/var/www/html/public/cached-media', $newFile);
        if (File::exists($newFile)) {
            $img = Image::make($newFile);
        } else {
            try {
                $d = dirname($newFile);
                if (!File::isDirectory($d)) {
                    File::makeDirectory($d, 777, true);
                }
                $img = Image::make($file)->resize($w, $h, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $img->save($newFile, 50, $newExtension);
            }catch (Throwable $e){
                return redirect(url('media/' . $slug));
                return (Image::make($originFile))->response();
            }

        }
        return $img->response();
    })->where('slug', '.*');

    Route::post('/bank/ok', [\App\Http\Controllers\BankController::class, 'ok']);
    Route::post('/bank/fail', [\App\Http\Controllers\BankController::class, 'fail']);

});




require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';
