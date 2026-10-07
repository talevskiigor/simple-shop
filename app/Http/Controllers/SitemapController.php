<?php

namespace App\Http\Controllers;

use App\Models\{Category, Page, Product};
use Spatie\Sitemap\Sitemap;

class SitemapController extends Controller
{
    public function __invoke(): Sitemap
    {
        $sitemap = Sitemap::create()
            ->add(url('/'))
            ->add(url('/contact'));

        foreach (Category::orderBy('id')->get() as $category) {
            $sitemap->add(url('categories/'.$category->slug));
        }

        return $sitemap
            ->add(Page::where('published', true)->orderBy('id')->get())
            ->add(Product::where('active', true)->with('media')->orderBy('id')->get());
    }
}
