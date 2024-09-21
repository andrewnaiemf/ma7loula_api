<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'lat', 'lon', 'user_id', 'details', 'city_id', 'state_id'];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function city(){
        return $this->belongsTo(City::class);
    }

    public function state(){
        return $this->belongsTo(State::class);
    }

    public function getIsDefaultAttribute(){
        return $this->user->client->default_address_id == $this->id;
    }
}
