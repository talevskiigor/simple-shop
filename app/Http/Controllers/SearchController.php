<?php
namespace App\Http\Controllers;
use App\Helpers\Image;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
class SearchController extends Controller {
    public function suggest(Request $request) {
        $data = $request->validate(['find' => 'nullable|string|max:200']);
        $query = trim($data['find'] ?? '');
        if (mb_strlen($query) < 2) return response()->json(['items' => [], 'total' => 0]);
        $items = Product::search(Str::ascii($query))->query(fn ($q) => $q->where('active', true))->get();
        return response()->json(['total' => $items->count(), 'items' => $items->take(6)->map(fn ($item) => [
            'name' => $item->name, 'url' => route('product.show', $item->slug),
            'image' => Image::get($item->image, 64), 'in_stock' => $item->quantity > 0,
            'price' => number_format($item->getPrice(), 0, ',', '.').' ден.',
        ])->values()])->header('Cache-Control', 'no-store');
    }
}
