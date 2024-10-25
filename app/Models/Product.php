<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Traits\HasMedia;

class Product extends Model
{
    use HasFactory, SoftDeletes, HasMedia;

    public const BatteriesCategory = 1;
    public const TiresCategory = 2;

    protected $fillable = ['name', 'description', 'category_id', 'brand_id', 'vendor_id', 'price', 'price_before_discount', 'stock', 'status', 'default_media_id'];

    public function cars()
    {
        return $this->belongsToMany(Car::class, 'product_car');
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function brand()
    {
        return $this->belongsTo(ProductBrand::class, 'brand_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function thumbnail()
    {
        return $this->belongsTo(Media::class, 'default_media_id');
    }

    public function attrs()
    {
        return $this->hasMany(ProductAttribute::class);
    }
}
