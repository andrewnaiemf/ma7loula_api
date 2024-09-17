<?php

namespace Modules\Core\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Modules\Core\Exceptions\HttpErrorException;
use Modules\Core\Plugins\SMS\SendSMSJob;

class RegisterService{

    public function sendOTP(string $phone): int{
        $ttl = 120;

        if(Cache::has('send_otp_to_'.$phone)){
            /** @var Carbon $time */
            $time = Cache::get('send_otp_to_'.$phone);
            throw new HttpErrorException(trans('Core::messages.auth.otp_limit', ['time'=> $time->diffForHumans()]), [], 422);
        }else{
            Cache::put('send_otp_to_'.$phone, Carbon::now()->addSeconds($ttl) , $ttl);
        }

        $otp = rand(100000, 999999);
        SendSMSJob::dispatch($phone, $otp);
        return $otp;
    }

}