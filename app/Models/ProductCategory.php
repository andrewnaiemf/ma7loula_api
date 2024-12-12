<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\Traits\Translatable;

class ProductCategory extends BaseModel
{
    use HasFactory, SoftDeletes, Translatable;

    public $translatable = ['name'];

    public function subCategories()
    {
        return $this->hasMany(ProductCategory::class, 'parent_id', 'id');
    }

    public function media()
    {
        return $this->belongsTo(Media::class);
    }

    public function getImageAttribute()
    {
        return $this->media ? $this->media->file_url : null;
    }
}
