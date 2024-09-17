<?php

namespace Modules\Core\Plugins\SMS;

class SMSSender implements SMSInterface{
    public function send(string $phoneNumber, string $message): bool{
        return true;
    }
}