<?php

function melipayamak_send_sms($to , $message)
{
    $postRequestSms = function ($url , $data) {
        $params = http_build_query($data);
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => ["Content-Type: application/x-www-form-urlencoded"],
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $params,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true
        );

        $ch = curl_init();
        curl_setopt_array($ch, $options);

        $response = curl_exec($ch);

        curl_close($ch);

        return $response;
    };

    $status = 0;

    $body = ["username" => env("USERNAME_MELIPAYAMAK"), "password" => env("PASSWORD_MELIPAYAMAK"), "to" => $to, "from" => env("FROM_MELIPAYAMAK"), "text" => $message, "isflash" => 'false'];

    $response = $postRequestSms('http://api.payamak-panel.com/post/send.asmx/SendSimpleSMS', $body);

    $xml = [];

    try {
       $xml = new SimpleXMLElement($response);
    } catch (Error $e) {
        
    }

    $response = $xml ? (array) $xml : ["string" => 0];
    $response = trim($response['string']);

    if (100 < $response) $status = 1;
    else $status = 0;

    return $status;
}
