<?php

namespace App\Traits;

trait NotificationTrait
{
    public function push_notification($message, $title, array $tokens)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';


        $serverKey = env('FCM_SERVER_KEY'); // ADD SERVER KEY HERE PROVIDED BY FCM

        $data = [
            "to" => $tokens[0],
            "notification" => [
                "title" => $title,
                "body" => $message,
                "icon" => 'https://memorization-management-system.com/assets/images/favicon.ico',
            ],
        ];
        $encodedData = json_encode($data);

        $headers = [
            'Authorization:key=' . $serverKey,
            'Content-Type: application/json',
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);
        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        // Close connection
        curl_close($ch);
        // FCM response
//       dd($result);
    }
}
