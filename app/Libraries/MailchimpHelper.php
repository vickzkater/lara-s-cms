<?php

namespace App\Libraries;

/**
 * Mailchimp API Helper - Laravel Library
 * Worked on Mailchimp API v3
 *
 * Copyright (c) KINIDI Tech and other contributors
 * Released under the MIT license.
 * For more information, see https://kiniditech.com/ or https://github.com/vickzkater
 *
 * https://github.com/vickzkater/mailchimp-helper-laravel
 */

use Mailchimp;

class MailchimpHelper
{
    public static function list()
    {
        // Get an array of all available lists:
        $response = Mailchimp::getLists();

        return $response;
    }

    public static function status($email_address)
    {
        // Check the staus of a subscriber:
        $list_id = env('MC_LIST_ID');
        $response = Mailchimp::status($list_id, $email_address);
        // Returns 'subscribed', 'unsubscribed', 'cleaned', 'pending', 'archived', 'transactional' or 'not found'

        return $response;
    }

    public static function hashing_subscribe($email_address)
    {
        /**
         * For example, to get the MD5 hash of the email address Urist.McVankab@freddiesjokes.com, 
         * first convert the address to its lowercase version: urist.mcvankab@freddiesjokes.com. 
         * The MD5 hash of urist.mcvankab@freddiesjokes.com is 62eeb292278cc15f5817cb78f7790b08.
         */

        return md5(strtolower($email_address));
    }

    public static function add_subscribe($email_address, $merge = [], $confirm = false)
    {
        // Adds/updates an existing subscriber:
        $list_id = env('MC_LIST_ID');
        // $merge = [
        //     'FNAME' => 'Vicky',
        //     'LNAME' => 'Budiman',
        //     'PHONE' => '+628123123123'
        // ];
        Mailchimp::subscribe($list_id, $email_address, $merge, $confirm);
        // Use $confirm = false to skip double-opt-in if you already have permission.
        // This method will update an existing subscriber and will not ask an existing subscriber to re-confirm.

        return true;
    }

    public static function add_tag($tag_name)
    {
        // Create a new tag
        $list_id = env('MC_LIST_ID');
        $method = 'POST';
        $endpoint = '/lists/' . $list_id . '/segments';
        $data = [
            'name' => $tag_name,
            'static_segment' => []
        ];
        $response = Mailchimp::api($method, $endpoint, $data); // Returns an array.

        return $response;
    }

    public static function get_tags()
    {
        /**
         * segments = tags
         * Get tags list
         */
        $list_id = env('MC_LIST_ID');
        $method = 'GET';
        $endpoint = '/lists/' . $list_id . '/segments';
        $data = [];
        $response = Mailchimp::api($method, $endpoint, $data); // Returns an array.

        return $response;
    }

    public static function add_tag_to_contact($email_address, $tag_id)
    {
        // First, check status of contact
        $status = MailchimpHelper::status($email_address);
        if ($status == 'not found') {
            return 'contact not found, please subscribe the list first';
        }
        // Add a tag to a contact
        $list_id = env('MC_LIST_ID');
        $method = 'POST';
        $endpoint = '/lists/' . $list_id . '/segments/' . $tag_id . '/members';
        $data = [
            'email_address' => $email_address
        ];
        $response = Mailchimp::api($method, $endpoint, $data); // Returns an array.

        return $response;
    }

    public static function view_tags_in_contact($email_address)
    {
        // First, check status of contact
        $status = MailchimpHelper::status($email_address);
        if ($status == 'not found') {
            return 'contact not found, please subscribe the list first';
        }
        // View tags
        $list_id = env('MC_LIST_ID');
        $subscriber_hash = MailchimpHelper::hashing_subscribe($email_address);
        $method = 'GET';
        $endpoint = '/lists/' . $list_id . '/members/' . $subscriber_hash . '/tags';
        $data = [];
        $response = Mailchimp::api($method, $endpoint, $data); // Returns an array.

        return $response;
    }

    public static function check_subscribe_status($email_address)
    {
        // Check to see if an email address is subscribed to a list:
        $list_id = env('MC_LIST_ID');
        $response = Mailchimp::check($list_id, $email_address); // Returns boolean

        return $response;
    }
}
