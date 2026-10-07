<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Product, Media, Category};
use App\Services\{ContentHtml, MediaFiles};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\{Rule, ValidationException};

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()->latest('id');
        if ($request->filled('q')) $query->where(fn ($q) => $q->where('name', 'like', '%'.$request->query('q').'%')->orWhere('model', 'like', '%'.$request->query('q').'%'));
        if ($request->query('stock') === 'out') $query->where('quantity', '<=', 0);
        return view('admin.products.index', ['products' => $query->paginate(30)->withQueryString()]);
    }
    public function create() { return $this->form(new Product(['active' => true, 'quantity' => 1, 'price' => 0, 'discount' => 0])); }
    public function edit(Product $product) { return $this->form($product); }
    private function form(Product $item) { return view('admin.products.form', ['item' => $item, 'catalogCategories' => Category::orderBy('name')->get()]); }
    public function store(Request $request) { return $this->save($request, new Product); }
    public function update(Request $request, Product $product) { return $this->save($request, $product); }

    private function save(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255', 'slug' => ['required', 'string', 'max:255', 'regex:/^[\pL\pN_-]+$/u', Rule::unique('products')->ignore($product)],
            'model' => 'required|string|max:255', 'description' => 'nullable|string|max:200000',
            'price' => 'required|numeric|min:0|max:9999999999.99|decimal:0,2', 'discount' => 'nullable|numeric|between:0,100|decimal:0,2',
            'quantity' => 'required|integer|min:0|max:100000', 'active' => 'required|boolean',
            'category_ids' => 'required|array|min:1', 'category_ids.*' => ['integer', 'distinct', Rule::exists('categories', 'id')->whereNull('deleted_at')],
            'media_ids' => 'required|array|min:1|max:30', 'media_ids.*' => ['integer', 'distinct', Rule::exists('media', 'id')->whereNull('deleted_at')],
        ]);
        $media = Media::whereIn('id', $data['media_ids'])->get()->keyBy('id');
        $cover = $media->get($data['media_ids'][0]);
        if ($cover->type !== 'image') throw ValidationException::withMessages(['media_ids' => 'The first gallery item must be an image.']);
        foreach ($media as $file) if (!app(MediaFiles::class)->resolve($file->path)) throw ValidationException::withMessages(['media_ids' => 'A selected file is missing. Choose an available file from the library.']);
        DB::transaction(function () use ($data, $product, $cover) {
            $product->fill(collect($data)->except(['category_ids', 'media_ids'])->all());
            $product->description = app(ContentHtml::class)->clean($data['description'] ?? '');
            $product->image = $cover->path;
            $product->discount = $data['discount'] ?? 0;
            $product->tax_id = $product->tax_id ?? 1;
            $product->save();
            $product->category()->sync($data['category_ids']);
            // Reinsert in selection order; the first item is also the cover.
            $product->media()->detach();
            foreach ($data['media_ids'] as $position => $id) $product->media()->attach($id, ['position' => $position]);
        });
        return redirect()->route('product.edit', $product)->with('status', 'Product saved.');
    }
    public function destroy(Product $product) { $product->delete(); return redirect()->route('product.index')->with('status', 'Product archived. Its media and order history are retained.'); }
}
