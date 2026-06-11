<?php

use App\Livewire\ProductDetail;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

it('mounts with a null product', function () {
    Livewire::test(ProductDetail::class)
        ->assertSet('product', null);
});

it('sets the product when product-selected event is received', function () {
    $product = Product::factory()->for(Category::factory())->create();

    Livewire::test(ProductDetail::class)
        ->dispatch('product-selected', productId: $product->id)
        ->assertSet('product.id', $product->id)
        ->assertSet('product.name', $product->name);
});

it('eager-loads the category relationship', function () {
    $category = Category::factory()->create(['name' => 'Gadgets']);
    $product  = Product::factory()->create(['category_id' => $category->id]);

    $component = Livewire::test(ProductDetail::class);
    $component->dispatch('product-selected', productId: $product->id);

    expect($component->get('product')->category->name)->toBe('Gadgets');
});
