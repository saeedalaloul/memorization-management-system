<?php

namespace App\Traits;

use GuzzleHttp\Exception\ClientException;
use Netflie\WhatsAppCloudApi\Message\Template\Component;
use Netflie\WhatsAppCloudApi\Response\ResponseException;
use Netflie\WhatsAppCloudApi\WhatsAppCloudApi;

trait SendMessageWhatsappApiTrait
{

    public function push_message($to_number, $component_body)
    {
        $serverToken = env('WHATSAPP_SERVER_TOKEN'); // ADD SERVER KEY HERE PROVIDED BY WHATSAPP

        $component_header = [];

        $component_buttons = [];

        // Instantiate the WhatsAppCloudApi super class.
        $whatsapp_cloud_api = new WhatsAppCloudApi([
            'from_phone_number_id' => env('WHATSAPP_FROM_PHONE_NUMBER_ID'),
            'access_token' => $serverToken,
        ]);

        $components = new Component($component_header, $component_body, $component_buttons);
        try {
           return $whatsapp_cloud_api->sendTemplate($to_number, 'report_month_quran_1', 'ar', $components)->httpStatusCode();
        } catch (ClientException | ResponseException $e) {
           return $e->getCode();
        }
        // Language is optional
    }
}
