<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;


class Media extends BaseModel
{
    use HasFactory;

    protected $fillable = ['model_type', 'model_id', 'path', 'filename'];

    public function getFileUrlAttribute(){
        return url($this->path .'/'.$this->filename);
    }
}
