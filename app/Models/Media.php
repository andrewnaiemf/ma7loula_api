<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;


class Media extends BaseModel
{
    use HasFactory;

    protected $fillable = ['model_type', 'model_id', 'path', 'filename'];

    public function getFileUrlAttribute(){
       // return url('storage/products/' . $this->filename);
        //return $this->filename;
       // return url($this->path .'/'.$this->filename);
       if($this->platform == 'dashboard'){
       // return url('storage/' . $this->path);
        if (!str_contains($this->path, 'storage')) {
          return url('storage/' . $this->path);
        }

        return url(trim($this->path));          
       }else{
          
         if (!str_contains($this->filename, 'storage/products')) {
          return url('storage/products/' . $this->filename);
        }
        
        else if (!str_contains($this->filename, 'storage')) {
          return url('storage/' . $this->filename);
        }

        return url(trim($this->filename));          
       }

       
    }
}
