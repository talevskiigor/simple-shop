<?php

namespace App\Services;

use App\Models\Product;
use JsonSerializable;

class CartItem implements JsonSerializable
{
    public int $id;
    public string $name;
    public float $price;
    public int $quantity = 1;
    public int $baseMinor;
    public int $totalMinor;

    public function __construct(public Product $associatedModel)
    {
        $this->id = $associatedModel->id;
        $this->name = $associatedModel->name;
        $this->baseMinor = (int) round((float) $associatedModel->price * 100);
        $discountBasisPoints = (int) round(max(0, min(100, (float) $associatedModel->discount)) * 100);
        $this->totalMinor = intdiv($this->baseMinor * (10000 - $discountBasisPoints) + 5000, 10000);
        $this->price = $this->totalMinor / 100;
    }

    public function getPriceSum(): float { return $this->price; }

    public function jsonSerialize(): array
    {
        return ['id' => $this->id, 'name' => $this->name, 'price' => $this->price, 'quantity' => 1,
            'associatedModel' => $this->associatedModel->only(['id', 'slug', 'name', 'model', 'image', 'price', 'discount'])];
    }
}
