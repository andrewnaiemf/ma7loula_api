<?php

namespace Modules\Core\Traits\Auth;

use App\Models\Role;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

trait HasRoles
{

    public function roles(){
        return $this->belongsToMany(Role::class, 'user_role');
    }

    public function ScopeHasRole(Builder $query, string $code){
        return $query->whereHas('roles', function($q) use ($code){
            $q->where('code', $code);
        });
    }

    public function attachRole(string $code): bool
    {
        $role = Role::where('code', $code)->first();
        if($role){
            $this->roles()->attach($role->id);
            return true;
        }

        return false;
    }
}
