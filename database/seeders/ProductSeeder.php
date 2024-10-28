<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\Product;
use App\Models\ProductAttribute;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = collect([]);

        $products = Product::factory(20)->create([
            'brand_id' => 3,
            'category_id' => 5
        ]);

        $tires = Product::factory(20)->create([
            'brand_id' => 2,
            'category_id' => 2,
            'status' => 'published'
        ]);

        foreach($tires as $tire){
            ProductAttribute::create([
                'product_id' => $tire->id,
                'key' => 'tire-type',
                'value' => ['normal', 'flat'][rand(0,1)],
            ]);

            $height = rand(10,30);
            $width = rand(10,30);
            $length = rand(10,30);

            ProductAttribute::create([
                'product_id' => $tire->id,
                'key' => 'height',
                'value' => $height,
            ]);

            ProductAttribute::create([
                'product_id' => $tire->id,
                'key' => 'width',
                'value' => $width,
            ]);

            ProductAttribute::create([
                'product_id' => $tire->id,
                'key' => 'length',
                'value' => $length,
            ]);
        }

        $products = $products->merge($tires);
        

        $battries = Product::factory(20)->create([
            'brand_id' => 1,
            'category_id' => 1,
            'status' => 'published'
        ]);


        foreach ($battries as $battery) {
            ProductAttribute::create([
                'product_id' => $battery->id,
                'key' => 'voltage',
                'value' => ['30 A', '40 A'][rand(0, 1)],
            ]);
        }
        $products = $products->merge($battries);


        
        foreach ($products as $product) {
            $product->cars()->sync([1, 2, 3]);

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
