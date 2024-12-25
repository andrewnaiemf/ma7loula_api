<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\SoftDeletes;

class OrderEmergency extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['order_id', 'vendor_id', 'worker_id', 'description', 'record', 'lat', 'lon', 'location'];

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
