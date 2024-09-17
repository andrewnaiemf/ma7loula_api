<?php

namespace Modules\Core\Plugins\SMS;

trait smsable{
    public function send(string $message){
        if($this->phone){
            SendSMSJob::dispatch($this->phone, $message);
        }
    }
}