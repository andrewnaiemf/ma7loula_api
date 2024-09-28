<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::factory(100)->create([
            'brand_id' => 1,
            'category_id' => 5
        ]);
        foreach($products as $product){

            $product->cars()->sync([1,2,3]);

            $product->allMedia()->create([
                'path' => 'assets/temp/products',
                'filename' => '1.png'
            ]);

            $product->allMedia()->create([
                'path' => 'assets/temp/products',
                'filename' => '2.jpg'
            ]);

            $product->allMedia()->create([
                'path' => 'assets/temp/products',
                'filename' => '3.jpg'
            ]);

            $product->update([
                'default_media_id' => $product->allMedia()->inRandomOrder()->first()->id
            ]);
        }
    }
}
