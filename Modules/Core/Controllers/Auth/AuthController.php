<?php

namespace Modules\Core\Controllers\Auth;

use Illuminate\Support\Facades\RateLimiter;
use Modules\Core\Controllers\Controller;
use Modules\Core\Requests\Auth\LoginRequest;
use Modules\Core\Requests\Auth\RegisterRequest;
use Modules\Core\Requests\Auth\ResetPasswordOTPRequest;
use Modules\Core\Requests\Auth\ResetPasswordRequest;
use Modules\Core\Requests\Auth\SendOTPRequest;
use Modules\Core\Services\Auth\AuthService;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}

    public function sendOTP(SendOTPRequest $request)
    {
        $otp = $this->authService->sendOTP($request->input('phone'));
        return $this->successResponse([
            'otp' => $otp
        ], trans('Core::messages.auth.otp_sent'));
    }

    public function register(RegisterRequest $request)
    {
        $user = $this->authService->register($request);
        return $this->successResponse([
            'user' => $user
        ], trans('Core::messages.auth.register_success'));
    }

    public function login(LoginRequest $request)
    {
        $user = $this->authService->login($request);
        return $this->successResponse([
            'user' => $user
        ], trans('Core::messages.auth.login_success'));
    }

    public function resetPasswordOTP(ResetPasswordOTPRequest $request)
    {
        $otp = $this->authService->sendOTP($request->input('phone'));
        return $this->successResponse([
            'otp' => $otp
        ], trans('Core::messages.auth.otp_sent'));
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $user = $this->authService->resetPassword($request);
        return $this->successResponse([
            'user' => $user
        ], trans('Core::messages.auth.password_success_reset'));
    }
}
