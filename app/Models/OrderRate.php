<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderRate extends Model
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
