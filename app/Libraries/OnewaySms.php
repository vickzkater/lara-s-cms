<?php

namespace App\Libraries;

/**
 * One Way SMS Gateway API - Laravel Library
 * Worked on API Version 1.2
 *
 * Copyright (c) KINIDI Tech and other contributors
 * Released under the MIT license.
 * For more information, see https://kiniditech.com/ or https://github.com/vickzkater
 *
 * https://github.com/vickzkater/onewaysms-laravel
 */

use GuzzleHttp\Client;

class OnewaySms
{
    /**
     * @param String $mobile required | Destination mobile number must include country code (Example: 601234567)
     * @param String $message required | Text Message (MAX 459 chars = 3 SMS)
     * @param Boolean $debug optional
     * @param String $username optional | Username provided to user to connect to our service
     * @param String $password optional
     * 
     * @return Boolean/String
     */
    public static function send($mobile, $message, $debug = false, $username = null, $password = null)
    {
        if (!$username) {
            $username = env('ONEWAYSMS_AUTH_USER');
        }
        if (!$password) {
            $password = env('ONEWAYSMS_AUTH_PASS');
        }
        if (!$username || !$password || env('ONEWAYSMS_API')) {
            if ($debug) {
                return '0000 - Auth credentials is not set';
            } else {
                return false;
            }
        }

        $api = env('ONEWAYSMS_API');
        $client = new Client();

        $result = $client->request('GET', $api . '/api.aspx', [
            'query' => [
                'apiusername' => $username,
                'apipassword' => $password,
                'senderid' => 'INFO',
                'mobileno' => $mobile,
                'message' => $message,
                'languagetype' => 1
            ]
        ]);

        $response = json_decode($result->getBody()->getContents());

        // set return code
        $response_code = $response;
        if ($response_code > 0) {
            // Positive value – Success
            $response_status = true;
            $response_message = 'Success';
        } else {
            $response_status = false;
            switch ($response_code) {
                case -100:
                    $response_message = 'apipassname or apipassword is invalid';
                    break;
                case -200:
                    $response_message = 'senderid parameter is invalid';
                    break;
                case -300:
                    $response_message = 'mobileno parameter is invalid';
                    break;
                case -400:
                    $response_message = 'languagetype is invalid';
                    break;
                case -500:
                    $response_message = 'Invalid characters in message';
                    break;
                case -600:
                    $response_message = 'Insufficient credit balance';
                    break;

                default:
                    $response_message = 'Unknown reason';
                    break;
            }
        }

        if ($debug) {
            $result = [
                'status' => $response_status,
                'code' => $response_code,
                'message' => $response_message
            ];

            // result sample:
            // array:3 [▼
            //     "status" => false
            //     "code" => -100
            //     "message" => "apipassname or apipassword is invalid"
            // ]
        } else {
            $result = $response_status; // Boolean
        }

        return $result;
    }
}
