<?php

namespace App\Services;

use App\Helpers\ShoppingCart;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/** A one-unit guest cart. Only scalar product IDs are stored in the session. */
class Cart
{
    public static function session(?string $id = null): self
    {
        if (!session()->has(ShoppingCart::SHOPPING_CART_ID)) {
            session()->put(ShoppingCart::SHOPPING_CART_ID, $id ?: (string) Str::uuid());
        }
        return new self;
    }

    public function getContent(): Collection
    {
        $ids = array_values(array_unique(array_map('intval', (array) session('cart_v2', []))));
        $products = $ids ? Product::whereIn('id', $ids)->where('active', true)->where('quantity', '>', 0)->get() : collect();
        session()->put('cart_v2', $products->pluck('id')->all());
        return $products->mapWithKeys(fn (Product $product) => [$product->id => new CartItem($product)]);
    }

    public function add(array $item): self
    {
        $product = Product::find($item['id']);
        if ($product && $product->active && $product->quantity > 0) {
            session()->put('cart_v2', array_values(array_unique([...session('cart_v2', []), $product->id])));
        }
        return $this;
    }

    public function remove(int|string $id): void
    {
        session()->put('cart_v2', array_values(array_filter(session('cart_v2', []), fn ($value) => (int) $value !== (int) $id)));
    }

    public function isEmpty(): bool { return $this->getContent()->isEmpty(); }
    public function getTotalQuantity(): int { return $this->getContent()->count(); }
    public function getTotal(): float { return $this->getContent()->sum(fn ($item) => $item->totalMinor) / 100; }
    public function getSubTotalWithoutConditions(): float { return $this->getContent()->sum(fn ($item) => $item->baseMinor) / 100; }
}
