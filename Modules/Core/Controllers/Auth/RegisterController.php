<?php

namespace Modules\Core\Controllers\Auth;

use Modules\Core\Controllers\Controller;
use Modules\Core\Requests\Auth\RegisterRequest;
use Modules\Core\Requests\Auth\SendOTPRequest;
use Modules\Core\Services\RegisterService;

class RegisterController extends Controller
{
    public function __construct(private RegisterService $registerService)
    {
        
    }

    public function sendOTP(SendOTPRequest $request){
        $otp = $this->registerService->sendOTP($request->input('phone'));
        return $this->successResponse(trans('Core::messages.auth.otp_sent'), [
            'otp' => $otp
        ]);
    }

    public function register(RegisterRequest $request){

    }
}