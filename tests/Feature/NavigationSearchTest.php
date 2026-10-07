<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Scout\EngineManager;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class NavigationSearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // The collection driver applies Product::toSearchableArray() to database records.
        // SQL's database driver compares the original Cyrillic columns instead.
        config(['scout.driver' => 'collection']);
        app(EngineManager::class)->forgetDrivers();
        $this->withoutVite();

        Product::create([
            'id' => 1,
            'name' => 'Трицикл Playtime',
            'slug' => 'tricikl-playtime',
            'description' => '<p>Детско возило со заштитен покрив.</p>',
            'model' => 'TR-435', 'image' => 'images/tricikl.jpg',
            'price' => 5000, 'tax_id' => 1, 'quantity' => 3,
        ]);
        Product::create([
            'id' => 2,
            'name' => 'Сет коцки',
            'slug' => 'set-kocki',
            'description' => 'Шарени коцки за игра.',
            'model' => 'BL-100', 'image' => 'images/kocki.jpg',
            'price' => 1000, 'tax_id' => 1, 'quantity' => 2,
        ]);
    }

    public static function matchingQueries(): array
    {
        return [
            'Cyrillic name' => ['трицикл'],
            'uppercase Cyrillic name' => ['ТРИЦИКЛ'],
            'Latin name' => ['tricikl'],
            'mixed-case Latin name' => ['TriCiKl'],
            'English brand' => ['Playtime'],
            'Cyrillic description' => ['заштитен'],
            'Latin description' => ['zastiten'],
            'surrounding whitespace' => ['  трицикл  '],
        ];
    }

    #[DataProvider('matchingQueries')]
    public function test_navigation_search_returns_the_matching_product(string $query): void
    {
        $response = $this->get('/search?'.http_build_query(['find' => $query]));

        $response->assertOk()
            ->assertViewHas('items', fn ($items) => $items->modelKeys() === [1])
            ->assertSee('Трицикл Playtime')
            ->assertDontSee('Сет коцки');
    }

    public function test_live_suggestions_match_cyrillic_and_latin_and_hide_private_products(): void
    {
        Product::factory()->create(['name' => 'Hidden tricikl', 'active' => false]);
        $a = $this->getJson('/search/suggest?'.http_build_query(['find' => 'три']))->assertOk()->assertJsonCount(1, 'items')->json();
        $b = $this->getJson('/search/suggest?find=tri')->assertOk()->json();
        $this->assertSame($a, $b);
        $this->assertSame('Трицикл Playtime', $a['items'][0]['name']);
        $this->getJson('/search/suggest?find=t')->assertOk()->assertJsonCount(0, 'items');
        $this->getJson('/search/suggest?find[]=bad')->assertUnprocessable();
    }

    public function test_unmatched_search_shows_the_empty_state(): void
    {
        $this->get('/search?find=nonexistent-product')
            ->assertOk()
            ->assertViewHas('items', fn ($items) => $items->isEmpty())
            ->assertSee('Нема резултати.');
    }

    public function test_search_includes_sold_out_cards_but_excludes_hidden_and_archived_products(): void
    {
        Product::findOrFail(1)->update(['quantity' => 0]);
        Product::factory()->create(['name' => 'Hidden tricikl', 'active' => false]);
        Product::factory()->create(['name' => 'Archived tricikl'])->delete();

        foreach (['трицикл', 'tricikl'] as $query) {
            $this->get('/search?'.http_build_query(['find' => $query]))
                ->assertOk()
                ->assertViewHas('items', fn ($items) => $items->modelKeys() === [1])
                ->assertSee('Трицикл Playtime')
                ->assertSee('Нема на залиха.')
                ->assertDontSee('Hidden tricikl')
                ->assertDontSee('Archived tricikl');
        }
    }

    public function test_navigation_form_and_empty_search_remain_usable(): void
    {
        $this->get('/')->assertOk()
            ->assertSee('action="'.url('search').'"', false)
            ->assertSee('name="find"', false)
            ->assertSee('type="submit">Барај', false);

        $this->get('/search?find=')->assertOk()
            ->assertViewHas('items', fn ($items) => $items->modelKeys() === [2, 1]);
    }
}
