<?php

namespace Modules\Core\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Modules\Core\Controllers\Controller;
use Modules\Core\Requests\Auth\LoginRequest;
use Modules\Core\Requests\Auth\RegisterRequest;
use Modules\Core\Requests\Auth\ResetPasswordOTPRequest;
use Modules\Core\Requests\Auth\ResetPasswordRequest;
use Modules\Core\Requests\Auth\SendOTPRequest;
use Modules\Core\Requests\Auth\UpdatePasswordRequest;
use Modules\Core\Requests\Auth\UpdatePhoneRequest;
use Modules\Core\Requests\Auth\UpdateProfileRequest;
use Modules\Core\Requests\Auth\VerifyOTPRequest;
use Modules\Core\Services\AuthService;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}

    public function sendOTP(SendOTPRequest $request)
    {
        $otp = $this->authService->sendOTP($request->input('phone'));
        return $this->successResponse([
            'success' => true,
            'otp_for_testing' => config('app.debug') ? $otp : null
        ]);
    }

    public function verifyOTP(VerifyOTPRequest $request)
    {
        return $this->successResponse([
            'success' => true
        ]);
    }


    public function register(RegisterRequest $request)
    {
        $user = $this->authService->register($request);
        return $this->successResponse([
            'user' => $user
        ]);
    }

    public function login(LoginRequest $request)
    {
        $user = $this->authService->login($request);
        return $this->successResponse([
            'user' => $user
        ]);
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        return $this->successResponse([
            'user' => $this->authService->updateProfile($request)
        ]);
    }

    public function updatePhone(UpdatePhoneRequest $request)
    {
        return $this->successResponse([
            'user' => $this->authService->updatePhone($request)
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        return $this->successResponse([
            'user' => $this->authService->resetPassword($request)
        ]);
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        return $this->successResponse([
            'user' => $this->authService->updatePassword($request)
        ]);
    }

    public function userProfile()
    {
        return $this->successResponse([
            'user' => $this->authService->userProfile()
        ]);
    }

    
}
