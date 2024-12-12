<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;


class OrderRate extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'products',
        'services',
        'worker',
        'comment'
    ];
}
