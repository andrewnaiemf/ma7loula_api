<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    public function __construct()
    {
        if(method_exists($this, 'loadTranslations')){
            $this->loadTranslations();
        }

        parent::__construct();
    }
}
