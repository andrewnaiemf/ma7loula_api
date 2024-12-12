<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;


class WorkerAttribute extends BaseModel
{
    use HasFactory;

    protected $fillable = ['key', 'value', 'worker_id'];
}
