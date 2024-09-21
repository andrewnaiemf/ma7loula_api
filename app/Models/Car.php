<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Car extends Model
{
    use HasFactory, SoftDeletes;
    

    public function brand(){
        return $this->belongsTo(CarBrand::class, 'car_brand_id');
    }

    public function model(){
        return $this->belongsTo(CarModel::class, 'car_model_id');
    }
}
