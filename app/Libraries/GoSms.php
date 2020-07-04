<?php

namespace App\Libraries;

/**
 * GoSMSGateway API - Laravel Library
 * Worked on SMS API MODULE Version V.1.6
 *
 * Copyright (c) KINIDI Tech and other contributors
 * Released under the MIT license.
 * For more information, see https://kiniditech.com/ or https://github.com/vickzkater
 *
 * https://github.com/vickzkater/gosms-laravel
 */

use GuzzleHttp\Client;

class GoSms
{
    /**
     * *Notes:
     * When the type of message is Unicode (3), then the message must be in hexadecimal format.
     * For Example : こんにちは世界
     * Hexadecimal : 30533093306B3061306F4E16754C 
     * 
     * @param String $mobile required | Destination mobile number, Indonesian (08xxxxx) or international (628xxxx) format
     * @param String $message required | Text Message (encoded, max 5000 chars)
     * @param String $trxid required | Client TrxID, used to avoid duplicate sms (Optional, max 50 char, type string)
     * @param Integer $type optional | 0 (or missing) ascii text message (normal message) or 3 Unicode message (Arabic, Japan, Chinese, etc)
     * @param Boolean $debug optional
     * @param String $username optional | Username provided to user to connect to our service
     * @param String $password optional
     * 
     * @return Boolean/String
     */
    public static function send($mobile, $message, $trxid, $type = 0, $debug = false, $username = null, $password = null)
    {
        if (!$username) {
            $username = env('GOSMS_AUTH_USER');
        }
        if (!$password) {
            $password = env('GOSMS_AUTH_PASS');
        }
        if (!$username || !$password) {
            if ($debug) {
                return '0000 - Auth credentials is not set';
            } else {
                return false;
            }
        }

        $api = 'https://secure.gosmsgateway.com/masking/api';
        $client = new Client();

        $result = $client->request('GET', $api . '/sendSMS.php', [
            'query' => [
                'username' => $username,
                'mobile' => $mobile,
                'message' => $message,
                'auth' => md5($username . $password . $mobile),
                'trxid' => $trxid,
                'type' => $type
            ]
        ]);

        $response = json_decode($result->getBody()->getContents());

        // set return code
        $code['1701'] = 'Success';
        $code['1702'] = 'Invalid Username or Password';
        $code['1703'] = 'Internal Server Error';
        $code['1704'] = 'Data not found';
        $code['1705'] = 'Process Failed';
        $code['1706'] = 'Invalid Message';
        $code['1707'] = 'Invalid Number';
        $code['1708'] = 'Insufficient Credit';
        $code['1709'] = 'Group Empty';
        $code['1711'] = 'Invalid Group Name';
        $code['1712'] = 'Invalid Group ID';
        $code['1713'] = 'Invalid msgid';
        $code['1721'] = 'Invalid Phonebook Name';
        $code['1722'] = 'Invalid Phonebook ID';
        $code['1731'] = 'User Name already exist';
        $code['1732'] = 'Sender ID not valid';
        $code['1733'] = 'Internal Error – please contact administrator';
        $code['1734'] = 'Invalid client user name';
        $code['1735'] = 'Invalid Credit Value';

        if ($debug) {
            if (!isset($code[$response])) {
                return $response;
            } else {
                return $response . ' - ' . $code[$response];
            }
        } else {
            if (!isset($code[$response])) {
                return false;
            } elseif ($response == 1701) {
                return true;
            } else {
                return false;
            }
        }
    }
}
