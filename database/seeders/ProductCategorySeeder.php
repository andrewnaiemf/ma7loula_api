<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\ProductCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $media = Media::create([
            'model_type' => ProductCategory::class,
            'model_id' => 1,
            'path' => 'assets/temp/sliders',
            'filename' => '1.jpg'
        ]);

        $categories = [
            [
                'id' => 3,
                'name' => 'العفشة',
                'parent_id' => null,
                'media_id' => $media->id
            ],
            [
                'id' => 4,
                'name' => 'الكهرباء',
                'parent_id' => null,
                'media_id' => null
            ],
            [
                'id' => 5,
                'name' => 'الموتور',
                'parent_id' => null,
                'media_id' => null
            ],
            [
                'id' => 6,
                'name' => 'السمكرة',
                'parent_id' => null,
                'media_id' => null
            ],
            [
                'id' => 7,
                'name' => 'المساعدين',
                'parent_id' => 3,
                'media_id' => null
            ],
            [
                'id' => 8,
                'name' => 'مساعد خلفي',
                'parent_id' => 7,
                'media_id' => null
            ],
            [
                'id' => 9,
                'name' => 'مساعد يمين',
                'parent_id' => 7,
                'media_id' => null
            ],
            [
                'id' => 10,
                'name' => 'بوجيهات',
                'parent_id' => 4,
                'media_id' => null
            ],
            [
                'id' => 11,
                'name' => 'فيوز',
                'parent_id' => 4,
                'media_id' => null
            ],
        ];

        ProductCategory::insert($categories);
    }
}
