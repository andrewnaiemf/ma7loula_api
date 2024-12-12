<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;


class OrderStatus extends BaseModel
{
    use HasFactory;

    protected $fillable = ['status', 'order_id'];
}
