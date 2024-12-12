<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;


class Client extends BaseModel
{
    use HasFactory;

    protected $fillable = ['user_id', 'default_address_id', 'default_car_id'];

    public function defaultAddress(){
        return $this->belongsTo(Address::class, 'default_address_id');
    }

    public function defaultCar(){
        return $this->belongsTo(Car::class, 'default_car_id');
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function cars(){
        return $this->user->cars();
    }
}
