<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'lat', 'lon', 'user_id', 'client_id', 'details', 'city_id', 'state_id'];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function client(){
        return $this->belongsTo(Client::class);
    }

    public function city(){
        return $this->belongsTo(City::class);
    }

    public function state(){
        return $this->belongsTo(State::class);
    }

    public function getIsDefaultAttribute(){
        return $this->client->default_address_id == $this->id;
    }
}
