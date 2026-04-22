<?php
namespace Modules\Core\Traits;

use App\Models\Media;

trait HasMedia{
    
    public function media(){
        return $this->morphOne(Media::class, 'media', 'model_type', 'model_id')->whereNull('deleted_at');
    }

    public function allMedia(){
        return $this->morphMany(Media::class, 'media', 'model_type', 'model_id')->whereNull('deleted_at');
    }
}