<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Services\ContentHtml;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PageController extends Controller
{
    public function index() { return view('admin.pages.index', ['items' => Page::orderBy('title')->get()]); }
    public function create() { return view('admin.pages.form', ['item' => new Page(['published' => false])]); }
    public function edit(Page $page) { return view('admin.pages.form', ['item' => $page]); }
    public function store(Request $request) { return $this->save($request, new Page); }
    public function update(Request $request, Page $page) { return $this->save($request, $page); }
    private function save(Request $request, Page $page) {
        $data = $request->validate(['title' => 'required|string|max:255', 'slug' => ['required', 'max:255', 'regex:/^[\pL\pN_-]+$/u', Rule::unique('pages')->ignore($page)], 'body' => 'required|string|max:200000', 'published' => 'required|boolean']);
        $data['body'] = app(ContentHtml::class)->clean($data['body']);
        $page->fill($data)->save();
        return redirect()->route('pages.edit', $page)->with('status', 'Page saved.');
    }
    public function destroy(Page $page) { $page->delete(); return back()->with('status', 'Page archived.'); }
}
