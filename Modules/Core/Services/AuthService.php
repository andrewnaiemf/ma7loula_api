<?php

namespace Modules\Core\Services;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
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
    public function userWithAuthToken($user): JsonResource{
        $user->auth_token = $user->createToken('auth', ['*'], Carbon::now()->addDays(120))->plainTextToken;
        return $this->UserResource($user);
    }

    public function UserResource($user): JsonResource{
        return new ClientAuthResource($user);
    }

    public function createUser(Request $request): User{
        $data = $request->all(['name', 'email', 'phone', 'password']);

        $data['role_id'] = 2;

        return User::create($data);
    }

    public function sendOTP(string $phone): int
    {
        $otp = rand(100000, 999999);
        Cache::put('otp_for_'.$phone, $otp, 60*10);
        SendSMSJob::dispatch($phone, $otp);
        return $otp;
    }

    public function register(Request $request): JsonResource
    {
        $user = $this->createUser($request);

        Client::create([
            'user_id' => $user->id
        ]);

        return $this->userWithAuthToken($user);
    }

    public function updateProfile(UpdateProfileRequest $request): JsonResource
    {
        $data = $request->all(['name', 'email']);
        
        /**@var User $user */
        $user =Auth::user();
        $user->update($data);

        return $this->UserResource($user);
    }

    public function updatePhone(UpdatePhoneRequest $request): JsonResource
    {
        $data = $request->all(['phone']);
        
        /**@var User $user */
        $user = Auth::user();
        $user->update($data);

        return $this->UserResource($user);
    }

    public function login(LoginRequest $request, $role = 'client'): JsonResource | Throwable
    {
        $phone = $request->input('phone');
        $password = $request->input('password');

        if (
            Auth::attempt([
                'phone' => $phone,
                'password' => $password,
                fn(Builder $query) => $query->whereHas('role', function ($q) use ($role) 
                {
                    $q->where('code', $role);
                })
            ])
        ) {
            $user = User::where('phone', $phone)
            ->whereHas('role', function ($q) use ($role){
                $q->where('code', $role);
            })
            ->first();

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

        $header_to_role_id = [
            'client' => 2,
            'vendor_cp' => 3,
            'vendor_bt' => 4,
            'winch_driver' => 5,
            'worker_bt' => 6,
            'worker_sos' => 7
        ];

        $user = User::where('phone', $phone)
        ->where('role_id', $header_to_role_id[$request->header('App')])
        ->first();
        $user->update([
            'password' => $password
        ]);

        return $this->UserResource($user);
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

        return $this->UserResource($user);
    }

    public function userProfile(){
        /** @var User $user */
        $user = Auth::user();
        return $this->UserResource($user);
    }
}
