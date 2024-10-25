<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Modules\Core\Observers\UserObserver;
use Modules\Core\Plugins\SMS\smsable;
use Modules\Core\Traits\Auth\HasRoles;

#[ObservedBy([UserObserver::class])]
class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, smsable, HasRoles, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'default_address_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function cars(){
        return $this->belongsToMany(Car::class, 'user_car')->withPivot(['created_at', 'deleted_at']);
    }

    public function client(){
        return $this->hasOne(Client::class);
    }

    public function defaultAddress(){
        return $this->client->defaultAddress();
    }

    public function defaultCar(){
        return $this->client->defaultCar();
    }

    public function vendor(){
        return $this->hasOne(Vendor::class);
    }
}
