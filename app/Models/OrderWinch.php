<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\SoftDeletes;

class OrderWinch extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['order_id', 'distance_in_meters', 'duration_in_minutes', 'from_lat', 'from_lon', 'from_text', 'to_lat', 'to_lon', 'to_text', 'vendor_id', 'worker_id'];

    public function worker(){
        return $this->belongsTo(Worker::class);
    }

    public function order(){
        return $this->belongsTo(Order::class);
    }

    public function vendor(){
        return $this->belongsTo(Vendor::class);
    }
}
