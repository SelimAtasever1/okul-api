<?php 

namespace App\Services;

use Twilio\Rest\Client;

class SmsService
{
    protected $client;

    public function __construct() 
    {
        $this->client = new Client('twilio_sid', 'twilio_token');
    }

    public function send($to, $message, $countryCode = '+90') // default turkiye
    {
        $from = 'your_twilio_num'; 
        $to = $countryCode . substr($to, 1); 

        $this->client->messages->create($to, [
            'from' => $from,
            'body' => $message
        ]);
    }
}