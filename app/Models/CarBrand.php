<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarBrand extends Model
{
    use HasFactory, SoftDeletes;

    public function model(){
        return $this->hasOne(CarModel::class, 'car_brand_id', 'id');
    }
}
