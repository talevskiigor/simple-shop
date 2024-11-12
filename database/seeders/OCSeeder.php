<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Media;
use App\Models\Page;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Database\Factories\PageFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Sitemap\Sitemap;
use Termwind\Components\Raw;

class OCSeeder extends Seeder
{

    public function run(): void
    {

        if (false) {
            DB::table('products')->truncate();
            DB::table('media')->truncate();
            DB::table('categories')->truncate();
            DB::table('media_product')->truncate();
            DB::table('category_product')->truncate();
            DB::table('pages')->truncate();
            DB::table('users')->truncate();
            dd(0);
        }

        $admin = User::findOrNew(1);
        $admin->name = 'Admin';
        $admin->email = 'admin@domain.tld';
        $admin->password =  Hash::make( 'n1mdak0rg@9814K1D5');
        $admin->save();


        // ------------------
        $img_product = DB::connection('oc')
            ->table('product_to_category')
            ->get();

        foreach ($img_product as $img) {
            DB::table('category_product')->updateOrInsert([
                'product_id' => $img->product_id,
                'category_id' => $img->category_id,
            ]);
        }
        DB::table('category_product')->update(
            [
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );

        // ------------------
        $img_product = DB::connection('oc')
            ->table('product_image')
            ->get();

        foreach ($img_product as $img) {
            DB::table('media_product')->updateOrInsert([
                'product_id' => $img->product_id,
                'media_id' => $img->product_image_id,
            ]);
        }
        DB::table('media_product')->update(
            [
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),

            ]
        );


        // ------------------
        $cats = DB::connection('oc')
            ->table('category as c')
            ->join('category_description as cd', 'c.category_id', 'cd.category_id')
            ->get([
                'cd.name',
                'cd.category_id',
            ]);
        foreach ($cats as $cat) {
            $category = Category::findOrNew($cat->category_id);
            $category->fill(
                [
                    'id' => $cat->category_id,
                    'name' => $cat->name,
                    'slug' => Str::of($cat->name)->slug(),
                ]
            )->save();

        }
// ------------------
        $imgs = DB::connection('oc')
            ->table('product_image as c')
            ->get();

        foreach ($imgs as $img) {
            $media = Media::findOrNew($img->product_image_id);
            $media->fill(
                [
                    'id' => $img->product_image_id,
                    'name' => basename($img->image),
                    'path' => $img->image,
                    'file' => basename($img->image),
                    'type' => 'image'

                ]
            )->save();
        }
// ----------------

        $items = DB::connection('oc')
            ->table('product as p')
            ->join('product_description as d', 'p.product_id', 'd.product_id')
            ->get([
                'p.product_id',
                'p.model',
                'd.name',
                'd.description',
                'p.price',
                'p.product_id',
                'p.image',
                'p.quantity'
            ]);

        foreach ($items as $item) {
//            dd($item);
            $product = Product::findOrNew($item->product_id);
            $product->fill(
                [
                    'id' => $item->product_id,
                    'slug' => Str::of($item->name)->slug(),
                    'name' => $item->name,
                    'description' => $item->description,
                    'model' => $item->model,
                    'price' => $item->price,
                    'image' => $item->image,
                    'discount' => 0,
                    'tax_id' => 1,
                    'quantity' => $item->quantity,
                ]
            )->save();
        }

        $items = DB::connection('oc')
            ->table('information_description')
            ->get();
        foreach ($items as $item) {
            $page = Page::findOrNew($item->information_id);
            $page->fill(
                [
                    'id' => $item->information_id,
                    'title' => $item->title,
                    'slug' => Str::of($item->title)->slug(),
                    'body' => $item->description,
                ]
            )->save();
        }


        DB::table('products')
            ->update(['image' => DB::raw("REPLACE(image , 'catalog/', 'images/')")]);

        DB::table('media')
            ->update(['path' => DB::raw("REPLACE(path , 'catalog/', 'images/')")]);

        Sitemap::create(env('APP_URL'))
            ->add(url(''))
            ->add(url('contact'))
            ->add(Page::all())
            ->add(Product::all())
            ->writeToDisk('public', 'sitemap.xml');

        File::copyDirectory(
            "/var/www/forkids.mk/image/catalog",
            "/var/www/html/simple-shop/public/media/images"
        );


//        // Set discount
//        $data = DB::table('products')
//            ->join('category_product', 'products.id', 'category_product.product_id')
//            ->where('category_product.category_id', 63)
//            ->get('id');
//        $ids = [];
//        foreach ($data->all() as $i) {
//            $ids[] = $i->id;
//        }
//        DB::table('products')
//            ->whereIn('id', $ids)
//            ->update(['discount' => 15]);


    }
}
