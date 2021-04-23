<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

// MAIL
use App\Mail\MailTester;

// USE LIBRARIES
use App\Libraries\GoSms;
use App\Libraries\OnewaySms;
use App\Libraries\MailchimpHelper;

// MODELS
use App\Models\system\SysUser;

class DevController extends Controller
{
    /**
     * GOSMS
     */
    public function gosms_send(Request $request)
    {
        // SET THE PARAMETERS
        $mobile = $request->input('mobile_phone');
        $message = $request->input('message');
        $trxid = uniqid();
        $type = 0;
        $debug = true;

        $result = GoSms::send($mobile, $message, $trxid, $type, $debug);

        dd($result); // Boolean
    }

    /**
     * ONEWAYSMS
     */
    public function onewaysms_send(Request $request)
    {
        // SET THE PARAMETERS
        $mobile = $request->input('mobile_phone');
        $message = $request->input('message');
        $debug = false;

        $result = OnewaySms::send($mobile, $message, $debug);

        dd($result);
    }

    /**
     * MAILCHIMP
     */
    public function mailchimp_list()
    {
        $result = MailchimpHelper::list();

        dd($result);
    }

    public function mailchimp_status(Request $request)
    {
        // SET THE PARAMETERS
        $email_address = $request->input('email');
        $result = MailchimpHelper::status($email_address);

        dd($result);
    }

    public function mailchimp_subscribe(Request $request)
    {
        // SET THE PARAMETERS
        $email_address = $request->input('email');
        $result = MailchimpHelper::add_subscribe($email_address);

        dd($result);
    }

    public function mailchimp_tags()
    {
        $result = MailchimpHelper::get_tags();

        dd($result);
    }

    public function mailchimp_add_tag(Request $request)
    {
        // SET THE PARAMETERS
        $tag_name = $request->input('name');

        $result = MailchimpHelper::add_tag($tag_name);

        dd($result);
    }

    public function mailchimp_add_tag_to_contact(Request $request)
    {
        // SET THE PARAMETERS
        $email_address = $request->input('email');
        $tag_id = $request->input('tag_id');

        $result = MailchimpHelper::add_tag_to_contact($email_address, $tag_id);

        dd($result);
    }

    public function mailchimp_view_tags_in_contact(Request $request)
    {
        // SET THE PARAMETERS
        $email_address = $request->input('email');

        $result = MailchimpHelper::view_tags_in_contact($email_address);

        dd($result);
    }

    /**
     * EMAIL
     */
    public function email_send(Request $request)
    {
        // SET THE DATA
        $data = SysUser::find(1);

        // SET EMAIL SUBJECT
        $subject_email = 'Test Send Email';

        $email_address = $request->email;
        if ($request->send && !$email_address) {
            return 'Must set email as recipient in param email';
        }

        try {
            // SEND EMAIL
            if ($request->send) {
                // send email using SMTP
                Mail::to($email_address)->send(new MailTester($data, $subject_email));
            } else {
                // rendering email in browser
                return (new MailTester($data, $subject_email))->render();
            }
        } catch (\Exception $e) {
            // Debug via $e->getMessage();
            dd($e->getMessage());
            // return "We've got errors!";
        }

        return 'Successfully sent email to ' . $email_address;
    }

    /**
     * AMAZON S3
     */
    public function amazon_s3_view()
    {
        return view('_example.amazon_s3');
    }

    public function amazon_s3_upload(Request $request)
    {
        // Docs: https://laravel.com/docs/8.x/filesystem#specifying-a-disk

        // get uploaded file
        $uploaded_file = $request->file('uploaded_file');

        // set target disk
        $storage_disk = 's3';

        // target location path in disk
        $target_path = 'brand';

        // rename file
        $file_name = 'sample-' . time() . '.' . $uploaded_file->getClientOriginalExtension();

        // upload to Amazon S3
        $uploaded_path = $uploaded_file->storeAs(
            $target_path,
            $file_name,
            $storage_disk
        );

        // save the image to user data
        $uploaded_file_on_s3_url = 'https://' . env('AWS_BUCKET') . '/' . $uploaded_path;

        return redirect()
            ->route('dev.amazon_s3')
            ->with('result', $uploaded_file_on_s3_url);
    }
}
