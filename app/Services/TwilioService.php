<?php

namespace App\Services;

use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

class TwilioService
{
    protected $client;
    protected $from;

    public function __construct()
    {
        $this->client = new Client(
            config('services.twilio.sid'),
            config('services.twilio.token')
        );
        $this->from = config('services.twilio.from');
    }

    /**
     * Send OTP to phone number
     */
    public function sendOTP($phoneNumber, $otpCode)
    {
        try {
            $message = "Your verification code is: {$otpCode}. This code will expire in 5 minutes.";
            
            $message = $this->client->messages->create(
                $phoneNumber,
                [
                    'from' => $this->from,
                    'body' => $message
                ]
            );

            Log::info('OTP sent successfully', [
                'phone' => $phoneNumber,
                'message_sid' => $message->sid
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send OTP', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Generate random OTP code
     */
    public function generateOTP()
    {
        return str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
    }
}
