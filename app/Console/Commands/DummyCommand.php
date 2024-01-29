<?php

namespace App\Console\Commands;

use App\Models\Page;
use App\Models\Product;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\SitemapGenerator;

class DummyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dummy:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Sitemap::create(env('APP_URL'))
            ->add(url(''))
            ->add(url('contact'))
            ->add(Page::all())
            ->add(Product::all())
            ->writeToDisk('public', 'sitemap.xml');
    }
}
