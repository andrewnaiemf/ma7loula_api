<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\SoftDeletes;

class Worker extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id', 'vendor_id', 'car_plate_number', 'type'];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function vendor(){
        return $this->belongsTo(Vendor::class);
    }
}
