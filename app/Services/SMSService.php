<?php

namespace App\Services;

use \Exception;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class SMSService
{
    protected String $apiToken;
    protected String $apiHost;

    public function __construct($apiToken)
    {
        $this->apiToken = $apiToken;
        $this->apiHost = "gatewayapi.com";

    }

    public function sendSMS($phoneNumber, $message) : bool
    {
        $client = new Client();

        $response = $client->post(
            'https://'.$this->apiHost.'/rest/mtsms', [
                RequestOptions::AUTH => [
                    $this->apiToken,
                    ''
                ],
                RequestOptions::JSON => [
                    'sender' => 'VWS-CC-DEMO',
                    'recipients' => [ [ "msisdn" => intval('31'.substr($phoneNumber,1)) ]],
                    'message' => $message
                ]
            ]
        );

        return true;
    }

}
