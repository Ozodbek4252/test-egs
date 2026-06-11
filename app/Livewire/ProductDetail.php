<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Attributes\On;
use Livewire\Component;

class ProductDetail extends Component
{
    public ?Product $product = null;

    #[On('product-selected')]
    public function show(int $productId): void
    {
        $this->product = Product::with('category')->find($productId);
        $this->modal('product-detail')->show();
    }

    public function render()
    {
        return view('livewire.product-detail');
    }
}
