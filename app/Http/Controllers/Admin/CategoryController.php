<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index() { return view('admin.categories.index', ['items' => Category::withCount('product')->orderBy('name')->get()]); }
    public function create() { return view('admin.categories.form', ['item' => new Category]); }
    public function edit(Category $category) { return view('admin.categories.form', ['item' => $category]); }
    public function store(Request $request) { return $this->save($request, new Category); }
    public function update(Request $request, Category $category) { return $this->save($request, $category); }
    private function save(Request $request, Category $category) {
        $category->fill($request->validate(['name' => 'required|string|max:255', 'slug' => ['required', 'max:255', 'regex:/^[\pL\pN_-]+$/u', Rule::unique('categories')->ignore($category)]]))->save();
        return redirect()->route('categories.index')->with('status', 'Category saved.');
    }
    public function destroy(Category $category) {
        if ($category->product()->exists()) return back()->withErrors(['category' => 'Move products to another category before removing it.']);
        $category->delete(); return back()->with('status', 'Category archived.');
    }
}
