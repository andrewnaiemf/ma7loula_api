<?php

namespace Modules\Core\Services\Auth;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Modules\Client\Resources\Auth\ClientAuthResource;
use Modules\Core\Exceptions\HttpErrorException;
use Modules\Core\Interfaces\Auth\UserResource;
use Modules\Core\Plugins\SMS\SendSMSJob;
use Modules\Core\Requests\Auth\LoginRequest;
use Modules\Core\Requests\Auth\RegisterRequest;
use Modules\Core\Requests\Auth\ResetPasswordRequest;
use Throwable;

class AuthService
{
    private function appToRole(){
        $app = request()->header('app');

        $appToRole = [
            'client' => 'client'
        ];
        
        return $appToRole[$app]??'client';
    }

    private function userWithAuthToken($user){
        $user->auth_token = $user->createToken('auth', ['*'], Carbon::now()->addDays(120))->plainTextToken;
        return $this->UserResource($user);
    }

    private function UserResource($user){
        return new ClientAuthResource($user);
    }

    public function sendOTP(string $phone): int
    {
        $otp = rand(100000, 999999);
        SendSMSJob::dispatch($phone, $otp);
        return $otp;
    }

    public function register(RegisterRequest $request): UserResource
    {
        $data = $request->all(['name', 'email', 'phone', 'password']);

        $user = User::create($data);
        $user->attachRole($this->appToRole());

        return $this->userWithAuthToken($user);
    }

    public function login(LoginRequest $request): UserResource | Throwable
    {
        $phone = $request->input('phone');
        $password = $request->input('password');
        $role = $this->appToRole();

        if (
            Auth::attempt([
                'phone' => $phone,
                'password' => $password,
                fn(Builder $query) => $query->whereHas('roles', function ($q) use ($role) 
                {
                    $q->where('code', $role);
                })
            ])
        ) {
            $user = User::where('phone', $phone)->first();
            if ($user) {
                return $this->userWithAuthToken($user);
            }
        }

        throw new HttpErrorException(trans('auth.failed'));
    }

    public function resetPassword(ResetPasswordRequest $request): UserResource
    {
        $phone = $request->input('phone');
        $password = $request->input('password');

        $user = User::where('phone', $phone)->first();
        $user->update([
            'password' => $password
        ]);

        return $this->userWithAuthToken($user);
    }
}
