<?php

namespace Modules\Core\Plugins\SMS;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendSMSJob implements ShouldQueue{

    use Queueable;
    
    public function __construct(private string $phone, private string $message)
    {
        
    }

    public function handle(SMSInterface $sms){
        $sms->send($this->phone, $this->message);
    }
}