<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MetaWhatsAppService
{
    protected $apiUrl;
    protected $accessToken;

    public function __construct()
    {
        $this->apiUrl = config('services.whatsapp.api_url');
        $this->accessToken = config('services.whatsapp.access_token');
    }

    /**
     * Send text message
     * 
     * @param string $to Recipient phone number with country code (e.g. 15551234567)
     * @param string $message Text message to send
     * @return array API response
     */
    public function sendTextMessage(string $to, string $message)
    {
        $response = Http::withToken($this->accessToken)
            ->post($this->apiUrl, [
                "messaging_product" => "whatsapp",
                "to" => $to,
                "type" => "text",
                "text" => [
                    "body" => $message
                ],
            ]);

        if ($response->failed()) {
            throw new \Exception("WhatsApp API error: " . $response->body());
        }

        return $response->json();
    }
}
