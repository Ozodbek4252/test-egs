<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create());
    $this->category = Category::factory()->create();
    Product::factory(20)->create(['category_id' => $this->category->id]);
});

it('redirects guests to login', function () {
    auth()->logout();
    $this->get(route('products'))->assertRedirect(route('login'));
});

it('loads the first page of products on mount', function () {
    $component = Livewire::test('pages::products');
    expect($component->get('items'))->toHaveCount(6);
    expect($component->get('loaded'))->toBe(6);
});

it('appends the next batch on loadMore', function () {
    $component = Livewire::test('pages::products');
    $component->call('loadMore');
    expect($component->get('items'))->toHaveCount(12);
    expect($component->get('loaded'))->toBe(12);
});

it('filters products by search term and resets the list', function () {
    Product::factory()->create([
        'category_id' => $this->category->id,
        'name'        => 'Unique Gadget ZZZ',
    ]);

    $component = Livewire::test('pages::products')->set('search', 'Unique Gadget ZZZ');

    expect($component->get('items'))->toHaveCount(1);
    expect($component->get('items')[0]['name'])->toBe('Unique Gadget ZZZ');
    expect($component->get('loaded'))->toBe(1);
});

it('filters products by category and resets the list', function () {
    $other = Category::factory()->create();
    Product::factory(3)->create(['category_id' => $other->id]);

    $component = Livewire::test('pages::products')
        ->set('categoryId', (string) $other->id);

    expect($component->get('items'))->toHaveCount(3);
    expect($component->get('loaded'))->toBe(3);
});

it('dispatches product-selected event with the correct product id', function () {
    $product = Product::first();

    Livewire::test('pages::products')
        ->call('selectProduct', $product->id)
        ->assertDispatched('product-selected', productId: $product->id);
});
