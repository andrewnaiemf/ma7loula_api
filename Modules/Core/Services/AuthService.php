<?php

namespace Modules\Core\Services;

use App\Models\Client;
use App\Models\User;
use App\Models\UserFcmToken;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Modules\Client\Resources\ClientAuthResource;
use Modules\Core\Exceptions\HttpErrorException;
use Modules\Core\Helpers\AppToRole;
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

        $user = User::create($data);
        $this->persistFcmTokenIfPresent($request, $user);

        return $user;
    }

    public function sendOTP(string $phone): int
    {
        $otp = rand(100000, 999999);
        Cache::put('otp_for_'.$phone, $otp, 60*10);
       // SendSMSJob::dispatch($phone, $otp);
        $sms = new SendSMSJob($phone, $otp);
       $sms->handle(new \Modules\Core\Plugins\SMS\SMSSender());
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
                $this->persistFcmTokenIfPresent($request, $user);

                return $this->userWithAuthToken($user);
            }
        }

        throw new HttpErrorException(trans('auth.failed'));
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResource
    {
        $phone = $request->input('phone');
        $password = $request->input('password');

        $user = User::where('phone', $phone)
        ->where('role_id', AppToRole::getRoleId($request->header('App')))
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

    public function deleteAccount(){
        /** @var User $user */
        $user = Auth::user();
        $user->delete();
        return $this->UserResource($user);
    }

    protected function persistFcmTokenIfPresent(Request $request, User $user): void
    {
        $token = $request->input('fcm_token');
        if (! is_string($token) || $token === '') {
            return;
        }

        $deviceId = $request->input('device_id');
        if (is_string($deviceId) && $deviceId !== '') {
            UserFcmToken::query()
                ->where('user_id', $user->id)
                ->where('device_id', $deviceId)
                ->where('token_hash', '!=', hash('sha256', $token))
                ->delete();
        }

        $hash = hash('sha256', $token);
        UserFcmToken::updateOrCreate(
            ['token_hash' => $hash],
            [
                'user_id' => $user->id,
                'token' => $token,
                'device_id' => is_string($deviceId) && $deviceId !== '' ? $deviceId : null,
                'platform' => is_string($request->input('platform')) && $request->input('platform') !== ''
                    ? $request->input('platform')
                    : null,
            ]
        );

        $user->forceFill(['fcm_token' => $token])->save();
    }
}
