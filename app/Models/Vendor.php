<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'user_id', 'type', 'lat', 'lon', 'tax_no', 'company_licence_no', 'company_licence_expire_date', 'address', 'id_image', 'company_licence_image'];
}
