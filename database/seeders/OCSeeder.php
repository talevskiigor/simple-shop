<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Media;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OCSeeder extends Seeder
{

    public function run(): void
    {

        DB::table('products')->truncate();
        DB::table('media')->truncate();
        DB::table('categories')->truncate();
        DB::table('media_product')->truncate();
        DB::table('category_product')->truncate();


        // ------------------
        $img_product = DB::connection('oc')
            ->table('oc.product_to_category')
            ->get();

        foreach ($img_product as $img) {
            DB::table('category_product')->insert([
                'product_id'=>$img->product_id,
                'category_id'=>$img->category_id,
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ]);
        }


        // ------------------
        $img_product = DB::connection('oc')
            ->table('oc.product_image')
            ->get();

        foreach ($img_product as $img) {
            DB::table('media_product')->insert([
                'product_id'=>$img->product_id,
                'media_id'=>$img->product_image_id,
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now(),
            ]);
        }


        // ------------------
        $cats = DB::connection('oc')
            ->table('category as c')
            ->join('category_description as cd', 'c.category_id', 'cd.category_id')
            ->get([
                'cd.name',
                'cd.category_id',
            ]);
        foreach ($cats as $cat) {
            Category::factory()->create([
                'id' => $cat->category_id,
                'name' => $cat->name,
                'slug' => Str::of($cat->name)->slug(),
            ]);
        }
// ------------------
        $imgs = DB::connection('oc')
            ->table('product_image as c')
            ->get();

        foreach ($imgs as $img) {
            $media = Media::factory()->create([
                'id' => $img->product_image_id,
                'name' => basename($img->image),
                'path' => $img->image,
                'file' => basename($img->image),
                'type' => 'image'

            ]);
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
            $product = Product::factory()->create(
                [
                    'id' => $item->product_id,
                    'slug' => Str::of($item->name)->slug(),
                    'name' => $item->name,
                    'description' => $item->description,
                    'model' => $item->model,
                    'price' => $item->price,
                    'image' => $item->image,
                    'discount'=>0,
                    'tax_id' => 1,
                    'quantity' => $item->quantity,
                ]
            );

//            dd($item);



            $imgs = DB::connection('oc')->table('product_image')->where('product_id', $item->product_id)->get();

        }

    }
}
