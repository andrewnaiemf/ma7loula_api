<?php

namespace Modules\Core\Plugins\SMS;


interface SMSInterface{
    public function send(string $phoneNumber, string $message): bool;
}