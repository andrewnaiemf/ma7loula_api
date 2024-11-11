<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class OrderVendor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['order_id', 'vendor_id', 'worker_id', 'products_price', 'services_price', 'tax_price', 'delivery_price', 'total', 'status'];

    public function vendor(){
        return $this->belongsTo(Vendor::class);
    }

    public function order(){
        return $this->belongsTo(Order::class);
    }

    public function getDeliveryTimeAttribute($val)
    {
        return $val ? Carbon::createFromFormat('Y-m-d H:i:s', $val) : null;
    }
}
