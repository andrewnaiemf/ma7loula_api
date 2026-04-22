<?php

namespace Modules\Core\Plugins\SMS;

trait smsable{
    public function send(string $message){
        if($this->phone){
           // SendSMSJob::dispatch($this->phone, $message);
           $sms = new SendSMSJob($this->phone, $message);
            $sms->handle(new \Modules\Core\Plugins\SMS\SMSSender());
        }
    }
}