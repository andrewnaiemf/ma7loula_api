<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderEmergency extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['order_id', 'vendor_id', 'worker_id', 'description', 'record', 'lat', 'lon', 'location'];
}
