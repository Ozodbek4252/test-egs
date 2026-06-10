<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Electronics', 'Clothing', 'Books',
        ];

        foreach ($categories as $name) {
            $category = Category::create(['name' => $name]);
            Product::factory(12)->create(['category_id' => $category->id]);
        }
    }
}
