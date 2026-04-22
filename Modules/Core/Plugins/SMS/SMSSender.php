<?php

namespace Modules\Core\Plugins\SMS;
use Illuminate\Support\Str;

class SMSSender implements SMSInterface{
    public function send(string $phoneNumber, string $message): bool{
        
        $full_message = 'Your OTP is: '.$message.'                                                       Please use this code to complete your verification.
Do not share this code with anyone.';
        $smsID = (string) Str::uuid();
         $url = 'https://app.community-ads.com/SendSMSAPI/api/SMSSender/SendSMS';

        // Prepare the data payload
        $data = [
        "UserName"=> "MahloulaAPI", 
        "Password"=> "30=mCe?(I/", 
        "SMSText"=> $full_message, 
        "SMSLang"=> "e", 
        "SMSSender"=> "CommunityAD", 
        "SMSReceiver"=> $phoneNumber, 
        "SMSID"       => $smsID,
        ];

        // Initialize cURL
        $ch = curl_init($url);

        // Set cURL options
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);

        // Execute the request
        $response = curl_exec($ch);

        // Check for errors
        if(curl_errno($ch)){
           // echo 'Request Error:' . curl_error($ch);
           
        } else {
            // Decode and display the response
            $result = json_decode($response, true);
           // print_r($result);
        }

        // Close cURL
        curl_close($ch);
        
        return true;
    }
}