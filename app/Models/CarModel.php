<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\SoftDeletes;

class CarModel extends BaseModel
{
    use HasFactory, SoftDeletes;

    public function brand(){
        return $this->belongsTo(CarBrand::class, 'car_brand_id');
    }
}
