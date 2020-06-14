<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;

// library
use App\Libraries\Helper;

// Models
use App\Models\system\Customer;

// Mail
use App\Mail\CustomerRegistration;
use App\Mail\CustomerResetPassword;

class AuthController extends Controller
{
    /**
     * Show login form page
     */
    public function login()
    {
        // if user logged in then redirect to home page
        if (Session::get('user')) {
            return redirect()->route('web.home');
        }

        // enable/disable module user in web
        if (env('MODULE_USER', 'OFF') == 'OFF') {
            return redirect()->route('web.home');
        }

        return view('web.login');
    }

    /**
     * Processing user login
     */
    public function submit_login(Request $request)
    {
        // if user logged in then redirect to home page
        if (Session::get('user')) {
            return redirect()->route('web.home');
        }

        $validation = [
            'email' => 'required|email',
            'password' => 'required|min:6'
        ];

        $message    = [
            'required' => ':attribute ' . lang('must not be empty', $this->translation),
            'min' => ':attribute ' . lang('must be minimal', $this->translation) . ' :min ' . lang('characters', $this->translation)
        ];

        $names      = [
            'email' => ucwords(lang('email', $this->translation)),
            'password' => ucwords(lang('password', $this->translation))
        ];

        $this->validate($request, $validation, $message, $names);

        if (env('RECAPTCHA_SECRET_KEY')) {
            // reCAPTCHA checking...
            $recaptcha = Helper::validate_recaptcha($request->input('g-recaptcha-response'), env('RECAPTCHA_SECRET_KEY'));
            if (!$recaptcha) {
                // reCAPTCHA FAILED
                return back()->withInput()->with('error', lang('reCAPTCHA validation unsuccessful, please try again', $this->translation));
            }
        }

        // validating input for prevent SQL injection - BEGIN
        $email = Helper::validate_input_email($request->email);
        if (!$email) {
            return back()->withInput()->with('error', 'Email must be using format: username@domain.com');
        }

        $password = Helper::hashing_this($request->input('password'));

        // check user data
        $data = Customer::where('email', $email)
            ->where('password', $password)
            ->first();


        if (empty($data)) {
            return back()->withInput()->with('error', lang('Incorrect login details, please try again or forgot password if you forget the password', $this->translation));
        }

        if ($data->isDeleted != 0) {
            return back()->withInput()->with('error', lang('Sorry, you cannot login your account anymore because the account has been deleted', $this->translation));
        }

        switch ($data->status) {
            case '1':
                // unverified
                return back()->withInput()->with('error', lang('Sorry, please verify your email first to activate the account', $this->translation));
                break;

            case '2':
                // verified

                // SUCCESS LOGIN
                // set nickname for user
                $arr_name = explode(' ', $data->name);
                if (isset($arr_name[0])) {
                    $data->nickname = $arr_name[0];
                } else {
                    $data->nickname = $data->name;
                }

                $redirect_uri = route('web.home');

                // get redirect uri from session
                if (Session::has('redirect_uri')) {
                    $redirect_uri = Session::get('redirect_uri');
                }


                return redirect($redirect_uri)
                    ->with(Session::put('user', $data));
                break;

            default:
                // 0: disabled
                return back()->withInput()->with('error', lang('Sorry, you cannot login your account now because the account has been disabled', $this->translation));
                break;
        }
    }

    /**
     * Show register form page
     */
    public function register()
    {
        // if user logged in then redirect to home page
        if (Session::get('user')) {
            return redirect()->route('web.home');
        }

        // enable/disable module user in web
        if (env('MODULE_USER', 'OFF') == 'OFF') {
            return redirect()->route('web.home');
        }

        return view('web.register');
    }

    /**
     * Processing user register
     */
    public function submit_register(Request $request)
    {
        // if user logged in then redirect to home page
        if (Session::get('user')) {
            return redirect()->route('web.home');
        }

        $validation = [
            'email' => 'required|email|unique:customers,email',
            'password1' => 'required|min:6|confirmed',
            'fullname' => 'required',
            'phone1' => 'required|min:10|max:14',
            'agree' => 'required'
        ];

        $message    = [
            'required' => ':attribute ' . lang('must not be empty', $this->translation),
            'email.unique' => ':attribute ' . lang('has been used, please use another one or forgot password to reset your password', $this->translation),
            'phone1.unique' => ':attribute ' . lang('has been used, please use another one', $this->translation),
            'min' => ':attribute ' . lang('must be minimal', $this->translation) . ' :min ' . lang('characters', $this->translation),
            'confirmed' => lang('confirm', $this->translation) . ' :attribute ' . lang('must match', $this->translation)
        ];

        $names      = [
            'email' => ucwords(lang('email', $this->translation)),
            'password1' => ucwords(lang('password', $this->translation)),
            'fullname' => ucwords(lang('full name', $this->translation)),
            'phone1' => ucwords(lang('phone number', $this->translation)),
            'agree' => lang('Privacy Policy and Terms &amp; Conditions Agreement', $this->translation)
        ];

        $this->validate($request, $validation, $message, $names);

        if (env('RECAPTCHA_SECRET_KEY')) {
            // reCAPTCHA checking...
            $recaptcha = Helper::validate_recaptcha($request->input('g-recaptcha-response'), env('RECAPTCHA_SECRET_KEY'));
            if (!$recaptcha) {
                // reCAPTCHA FAILED
                return back()->withInput()->with('error', lang('reCAPTCHA validation unsuccessful, please try again', $this->translation));
            }
        }

        // validating input for prevent SQL injection - BEGIN
        $email = Helper::validate_input_email($request->email);
        if (!$email) {
            return back()->withInput()->with('error', lang('Email must be using format: username@domain.com', $this->translation));
        }

        $fullname = Helper::validate_input($request->fullname);
        if (!$fullname) {
            return back()->withInput()->with('error', ucwords(lang('full name', $this->translation)) . ' ' . lang('must be using alphabet only', $this->translation));
        }

        $phone = Helper::validate_input($request->phone1);
        // sanitize phone number: length(10-14 chars), for Indonesian (should start with "0" or "62")
        if (strlen($phone) < 10 || strlen($phone) > 14) {
            return back()->withInput()->with('error', lang('The length of a phone number is around 10-14 digits', $this->translation));
        }
        if (substr($phone, 0, 2) != '08') {
            return back()->withInput()->with('error', lang('Phone number must start with &quot;08&quot; - using format 08123456789', $this->translation));
        }

        $gender = (int) $request->gender; // 1: male | 2:female

        $address = Helper::validate_input_text($request->address);

        $newsletter = 0;
        if (!empty($request->newsletter)) {
            $newsletter = 1;
        }
        // validating input for prevent SQL injection - END

        // processing the data
        $data = new Customer();
        $data->name = $fullname;
        $data->email = $email;
        $data->phone = $phone;
        $data->password = Helper::hashing_this($request->input('password1'));
        $data->gender = $gender;
        $data->address = $address;
        $data->newsletter = $newsletter;
        $data->instagram = Helper::validate_input($request->instagram);
        $data->remember_token = Helper::generate_token($email);
        $data->status = 1;

        if ($data->save()) {
            // send email verification account
            $subject_email = 'Verifikasi Akun KJV';

            // return (new CustomerRegistration($data, $subject_email))->render(); // rendering email in browser
            Mail::to($email)->send(new CustomerRegistration($data, $subject_email));

            return redirect()->route('web.login')->with('success', lang('Your KJV account registration is successful, please confirm your email to activate your account', $this->translation));
        } else {
            return back()->withInput()->with('error', lang('Oops, failed to register your account. Please try again.', $this->translation));
        }
    }

    /**
     * Verifying user email based on token
     */
    public function verify_email($token)
    {
        // if user logged in then redirect to home page
        if (Session::get('user')) {
            return redirect()->route('web.home');
        }

        $email = Helper::validate_token($token);

        if (!Helper::validate_input_email($email)) {
            return redirect()
                ->route('web.verify_email_result')
                ->with('error', lang("Sorry, your token is invalid so we can't verify your email. Please recheck your link or request link verification again.", $this->translation));
        }

        $customer = Customer::where('email', $email)->where('remember_token', urlencode($token))->first();

        if (empty($customer)) {
            return redirect()
                ->route('web.verify_email_result')
                ->with('error', lang("Sorry, your token is unknown so we can't verify your email. Please recheck your link or request link verification again.", $this->translation));
        }

        // activate the account
        $account = Customer::find($customer->id);
        $account->email_verified_at = date('Y-m-d H:i:s');
        $account->status = 2; // verified
        $account->remember_token = null;

        if ($account->save()) {
            return redirect()
                ->route('web.verify_email_result')
                ->with('success', lang('Yeah! Your email has been verified and your account has been activated, please login.', $this->translation));
        }

        return redirect()
            ->route('web.verify_email_result')
            ->with('error', lang("Oops, there was a problem when we verified your account. Please try again or contact our Customer Service for help.", $this->translation));
    }

    /**
     * Show verification email result
     */
    public function verify_email_result()
    {
        // if user logged in then redirect to home page
        if (Session::get('user')) {
            return redirect()->route('web.home');
        }

        return view('web.verify_email');
    }

    /**
     * Show request email verification page
     */
    public function verify_email_request_page(Request $request)
    {
        // if user logged in then redirect to home page
        if (Session::get('user')) {
            return redirect()->route('web.home');
        }

        return view('web.verify_email_request');
    }

    /**
     * Request email verification
     */
    public function verify_email_request(Request $request)
    {
        // if user logged in then redirect to home page
        if (Session::get('user')) {
            return redirect()->route('web.home');
        }

        $validation = [
            'email' => 'required|email'
        ];

        $message    = [
            'required' => ':attribute ' . lang('must not be empty', $this->translation)
        ];

        $names      = [
            'email' => ucwords(lang('email', $this->translation))
        ];

        $this->validate($request, $validation, $message, $names);

        if (env('RECAPTCHA_SECRET_KEY')) {
            // reCAPTCHA checking...
            $recaptcha = Helper::validate_recaptcha($request->input('g-recaptcha-response'), env('RECAPTCHA_SECRET_KEY'));
            if (!$recaptcha) {
                // reCAPTCHA FAILED
                return back()->withInput()->with('error', lang('reCAPTCHA validation unsuccessful, please try again', $this->translation));
            }
        }

        // validating input for prevent SQL injection - BEGIN
        $email = Helper::validate_input_email($request->email);
        if (!$email) {
            return back()->withInput()->with('error', lang('Email must be using format: username@domain.com', $this->translation));
        }

        $data = Customer::where('email', $email)->first();

        if (empty($data)) {
            return back()->withInput()->with('error', lang('Email not registered, but you can register using this email', $this->translation));
        }

        if ($data->status == 2) {
            return back()->withInput()->with('info', lang("Your email has been verified, you don't need to request verification anymore", $this->translation));
        }

        if ($data->status == 0) {
            return back()->withInput()->with('warning', lang("Sorry, your account has been disabled. Please contact Our Customer Service for help.", $this->translation));
        }

        $periode_request_again = 21600; // 6 hours
        $tstamp_req = strtotime($data->request_email_at);
        if (!empty($data->request_email_at) && time() - $tstamp_req < $periode_request_again) {
            $message_resp[] = lang("Sorry, you made a verification email request at #request_email_at.", $this->translation, ['#request_email_at' => date('d-m-Y H:i:s', $tstamp_req)]);
            $message_resp[] = lang("Try deleting some emails from your INBOX and empty the TRASH folder before trying to request verification emails again.", $this->translation);
            $message_resp[] = lang('Please check your INBOX or SPAM in your email and make sure your INBOX is not full. Or wait a few more moments to submit a request again.', $this->translation);
            $messages = implode(' ', $message_resp);

            return back()->withInput()->with('warning', $messages);
        }

        if ($data->request_email_amount >= 3) {
            $message_resp[] = lang("Sorry, you have already submitted a verification email request #amount times.", $this->translation, ['#amount' => $data->request_email_amount]);
            $message_resp[] = lang("If you still don't get a verification email, please check your SPAM folder and make sure your INBOX isn't full.", $this->translation);
            $message_resp[] = lang("Or please send an email request for verification assistance to [#email_contact].", $this->translation, ['#email_contact' => 'contact@kjvmotosport.com']);
            $messages = implode(' ', $message_resp);

            return back()->withInput()->with('warning', $messages);
        }

        if ($data->banned_status != 'none') {
            $message_resp[] = lang("Sorry, your account status is #banned_status banned for the reason that #banned_reason", $this->translation, ['#banned_status' => $data->banned_status, '#banned_reason' => $data->banned_reason]);
            $messages = implode(' ', $message_resp);

            return back()->withInput()->with('warning', $messages);
        }

        // generate new token
        $data->remember_token = Helper::generate_token($email);

        // save request time for set limit send email
        $data->request_email_at = date('Y-m-d H:i:s');

        $data->request_email_amount += 1;

        if ($data->save()) {
            // send email verification account
            $subject_email = 'Permintaan Verifikasi Akun KJV';

            // return (new CustomerRegistration($data, $subject_email))->render(); // rendering email in browser
            Mail::to($email)->send(new CustomerRegistration($data, $subject_email));

            return redirect()
                ->route('web.login')
                ->with('success', lang('Successfully send verification email to you, please confirm your email to activate your account', $this->translation));
        } else {
            return back()->withInput()->with('error', lang('Oops, failed to request verification email. Please try again.', $this->translation));
        }
    }

    /**
     * Logout
     */
    public function logout()
    {
        // if user logged in then logout
        if (Session::get('user')) {
            Session::forget('user');
            return redirect()->route('web.home')->with('success', lang('Logout successfully', $this->translation));
        }
        return redirect()->route('web.home');
    }

    /**
     * Show request forgot password email page
     */
    public function forgot_password()
    {
        // if user logged in then redirect to home page
        if (Session::get('user')) {
            return redirect()->route('web.home');
        }

        return view('web.forgot_password');
    }

    /**
     * Request forgot password email
     */
    public function forgot_password_send(Request $request)
    {
        // if user logged in then redirect to home page
        if (Session::get('user')) {
            return redirect()->route('web.home');
        }

        $validation = [
            'email' => 'required|email'
        ];

        $message    = [
            'required' => ':attribute ' . lang('must not be empty', $this->translation)
        ];

        $names      = [
            'email' => ucwords(lang('email', $this->translation))
        ];

        $this->validate($request, $validation, $message, $names);

        if (env('RECAPTCHA_SECRET_KEY')) {
            // reCAPTCHA checking...
            $recaptcha = Helper::validate_recaptcha($request->input('g-recaptcha-response'), env('RECAPTCHA_SECRET_KEY'));
            if (!$recaptcha) {
                // reCAPTCHA FAILED
                return back()->withInput()->with('error', lang('reCAPTCHA validation unsuccessful, please try again', $this->translation));
            }
        }

        // validating input for prevent SQL injection - BEGIN
        $email = Helper::validate_input_email($request->email);
        if (!$email) {
            return back()->withInput()->with('error', 'Email must be using format: username@domain.com');
        }

        $data = Customer::where('email', $email)->first();

        if (empty($data)) {
            return back()->withInput()->with('error', lang('Email not registered, but you can register using this email', $this->translation));
        }

        if ($data->status == 0) {
            return back()->withInput()->with('warning', lang("Sorry, your account has been disabled. Please contact Our Customer Service for help.", $this->translation));
        }

        if ($data->status == 1) {
            $message_resp[] = lang('Sorry, please verify your email first to activate the account', $this->translation);
            $message_resp[] = lang('Request email verification at', $this->translation);
            $message_resp[] = route('web.verify_email_request_page');
            $messages = implode(' ', $message_resp);

            return back()->withInput()->with('warning', $messages);
        }

        // generate new token for reset password
        $data->remember_token = Helper::generate_token($email);

        if ($data->save()) {
            // send email verification for reset password
            $subject_email = 'Permintaan Reset Password Akun KJV';

            // return (new CustomerResetPassword($data, $subject_email))->render(); // rendering email in browser
            Mail::to($email)->send(new CustomerResetPassword($data, $subject_email));

            return redirect()
                ->route('web.login')
                ->with('success', lang('Successfully send reset password email to you, please confirm your email to reset your password', $this->translation));
        } else {
            return back()->withInput()->with('error', lang('Oops, failed to request reset password email. Please try again.', $this->translation));
        }
    }

    /**
     * Verify token for reset password
     */
    public function verify_resetpass($token)
    {
        // if user logged in then redirect to home page
        if (Session::get('user')) {
            return redirect()->route('web.home');
        }

        $error = '';
        $success = '';
        $email = Helper::validate_token($token);

        if (!Helper::validate_input_email($email)) {
            return view('web.verify_resetpass')
                ->with('error', lang("Sorry, your token is invalid so we can't verify your request to reset password. Please recheck your link or request reset password again.", $this->translation));
        } else {
            $customer = Customer::where('email', $email)->where('remember_token', urlencode($token))->first();

            if (empty($customer)) {
                $error = lang("Sorry, your token is unknown so we can't verify your request to reset password. Please recheck your link or request reset password again.", $this->translation);
            } else {
                $success = lang('Please input your new password', $this->translation);
            }
        }

        return view('web.verify_resetpass', compact('error', 'success', 'token'));
    }

    /**
     * Reser user password based on token
     */
    public function resetpass(Request $request)
    {
        // if user logged in then redirect to home page
        if (Session::get('user')) {
            return redirect()->route('web.home');
        }

        $validation = [
            'password1' => 'required|min:6|confirmed',
            'token' => 'required'
        ];

        $message    = [
            'required' => ':attribute ' . lang('must not be empty', $this->translation),
            'min' => ':attribute ' . lang('must be minimal', $this->translation) . ' :min ' . lang('characters', $this->translation),
            'confirmed' => lang('confirm', $this->translation) . ' :attribute ' . lang('must match', $this->translation)
        ];

        $names      = [
            'password1' => ucwords(lang('password', $this->translation)),
            'token' => ucwords(lang('token', $this->translation))
        ];

        $this->validate($request, $validation, $message, $names);

        if (env('RECAPTCHA_SECRET_KEY')) {
            // reCAPTCHA checking...
            $recaptcha = Helper::validate_recaptcha($request->input('g-recaptcha-response'), env('RECAPTCHA_SECRET_KEY'));
            if (!$recaptcha) {
                // reCAPTCHA FAILED
                return back()->withInput()->with('error', lang('reCAPTCHA validation unsuccessful, please try again', $this->translation));
            }
        }

        $token = $request->token;

        // get email from token
        $email = Helper::validate_token($token);
        if (!Helper::validate_input_email($email)) {
            return back()
                ->withInput()
                ->with('error', lang("Sorry, your token is invalid so we can't verify your request to reset password. Please recheck your link or request reset password again.", $this->translation));
        } else {
            $customer = Customer::where('email', $email)->where('remember_token', urlencode($token))->first();

            if (empty($customer)) {
                return back()
                    ->withInput()
                    ->with('error', lang("Sorry, your token is unknown so we can't verify your request to reset password. Please recheck your link or request reset password again.", $this->translation));
            } else {
                $user = Customer::find($customer->id);
                $user->password = Helper::hashing_this($request->input('password1'));
                $user->remember_token = null;

                if ($user->save()) {
                    return redirect()
                        ->route('web.login')
                        ->with('success', lang("Successfully reset your password, please login your account using new password.", $this->translation));
                }

                return back()
                    ->withInput()
                    ->with('error', lang("Sorry, failed to reset your password. Please try again later.", $this->translation));
            }
        }
    }

    public function profile()
    {
        // if user not logged in then redirect to login page
        if (!Session::get('user')) {
            return redirect()->route('web.login')->with('warning', lang('Please login first for access our website', $this->translation));
        }

        $user = Session::get('user');

        $data = Customer::find($user->id);

        return view('web.profile', compact('data'));
    }

    public function update_profile(Request $request)
    {
        // if user not logged in then redirect to login page
        if (!Session::get('user')) {
            return redirect()->route('web.login')->with('warning', lang('Please login first for access our website', $this->translation));
        }

        $validation = [
            'fullname' => 'required',
            'gender' => 'required|integer'
        ];

        if ($request->input('current_password')) {
            $validation['password1'] = 'required|min:6|confirmed';
        }

        $message    = [
            'required' => ':attribute ' . lang('must not be empty', $this->translation),
            'min' => ':attribute ' . lang('must be minimal', $this->translation) . ' :min ' . lang('characters', $this->translation),
            'confirmed' => lang('confirm', $this->translation) . ' :attribute ' . lang('must match', $this->translation)
        ];

        $names      = [
            'password1' => ucwords(lang('new password', $this->translation)),
            'fullname' => ucwords(lang('full name', $this->translation))
        ];

        $this->validate($request, $validation, $message, $names);

        // validating input for prevent SQL injection - BEGIN
        $fullname = Helper::validate_input($request->fullname);
        if (!$fullname) {
            return back()->withInput()->with('error', ucwords(lang('full name', $this->translation)) . ' ' . lang('must be using alphabet only', $this->translation));
        }

        $gender = (int) $request->gender; // 1: male | 2:female

        $address = Helper::validate_input_text($request->address);

        $user = Session::get('user');

        // get existing data
        $data = Customer::find($user->id);

        // check current password if change password
        if ($request->input('current_password')) {
            if ($data->password != Helper::hashing_this($request->input('current_password'))) {
                return back()->withInput()->with('error', (lang('Your current password is incorrect', $this->translation)));
            } else {
                $data->password = Helper::hashing_this($request->input('password1'));
            }
        }

        $data->name = $fullname;
        $data->gender = $gender;
        $data->address = $address;
        $data->instagram = Helper::validate_input($request->instagram);

        if ($data->save()) {
            return redirect()->route('web.profile')->with('success', lang('Successfully updated profile', $this->translation));
        }

        return back()->withInput()->with('error', lang('Oops, failed to update profile. Please try again.', $this->translation));
    }
}
