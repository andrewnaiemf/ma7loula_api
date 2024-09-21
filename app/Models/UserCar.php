<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserCar extends Model
{
    use SoftDeletes;
    
    protected $table = 'user_car';
    
    protected $fillable = ['user_id', 'car_id', 'client_id'];

    public function car(){
        return $this->belongsTo(Car::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function client(){
        return $this->belongsTo(Client::class);
    }
    
    public function getIsDefaultAttribute(){
        return $this->client->default_car_id == $this->car_id;
    }
}
