<?php

namespace Modules\Core\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Modules\Client\Resources\ClientAuthResource;
use Modules\Core\Exceptions\HttpErrorException;
use Modules\Core\Plugins\SMS\SendSMSJob;
use Modules\Core\Requests\Auth\LoginRequest;
use Modules\Core\Requests\Auth\RegisterRequest;
use Modules\Core\Requests\Auth\ResetPasswordRequest;
use Modules\Core\Requests\Auth\UpdatePasswordRequest;
use Modules\Core\Requests\Auth\UpdatePhoneRequest;
use Modules\Core\Requests\Auth\UpdateProfileRequest;
use Modules\Core\Requests\Auth\UpdateRequest;
use Modules\Core\Requests\Auth\VerifyOTPRequest;
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

    private function userWithAuthToken($user): JsonResource{
        $user->auth_token = $user->createToken('auth', ['*'], Carbon::now()->addDays(120))->plainTextToken;
        return $this->UserResource($user);
    }

    private function UserResource($user): JsonResource{
        return new ClientAuthResource($user);
    }

    public function sendOTP(string $phone): int
    {
        $otp = rand(100000, 999999);
        Cache::put('otp_for_'.$phone, $otp, 60);
        SendSMSJob::dispatch($phone, $otp);
        return $otp;
    }

    public function register(RegisterRequest $request): JsonResource
    {
        $data = $request->all(['name', 'email', 'phone', 'password']);

        $user = User::create($data);
        $user->attachRole($this->appToRole());

        return $this->userWithAuthToken($user);
    }

    public function updateProfile(UpdateProfileRequest $request): JsonResource
    {
        $data = $request->all(['name', 'email']);
        
        /**@var User $user */
        $user =Auth::user();
        $user->update($data);

        return $this->userWithAuthToken($user);
    }

    public function updatePhone(UpdatePhoneRequest $request): JsonResource
    {
        $data = $request->all(['phone']);
        
        /**@var User $user */
        $user = Auth::user();
        $user->update($data);

        return $this->userWithAuthToken($user);
    }

    public function login(LoginRequest $request): JsonResource | Throwable
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

    public function resetPassword(ResetPasswordRequest $request): JsonResource
    {
        $phone = $request->input('phone');
        $password = $request->input('password');

        $user = User::where('phone', $phone)->first();
        $user->update([
            'password' => $password
        ]);

        return $this->userWithAuthToken($user);
    }

    public function updatePassword(UpdatePasswordRequest $request){
        $data = $request->all(['password']);
        
        /** @var User $user */
        $user = Auth::user();
        if(Hash::check($request->input('current_password'), $user->password)){
            $user->update($data);
        }else{
            throw new HttpErrorException('wrong password', [], 422);
        }

        return $this->userWithAuthToken($user);
    }
}
