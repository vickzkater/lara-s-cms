<?php

namespace App\Http\Controllers\Admin\system;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Socialite;

// LIBRARIES
use App\Libraries\Helper;

// MODELS
use App\Models\system\SysLog;
use App\Models\system\SysUser;
use App\Models\system\SysGroupRule;
use App\Models\system\SysGroupBranch;

class AuthController extends Controller
{
    protected $providers = [
        'google', 'facebook', 'twitter', 'instagram'
    ];

    public function login()
    {
        if (Session::get('admin')) {
            return redirect()->route('admin.home');
        }
        return view('admin.system.login');
    }

    public function do_login(Request $request)
    {
        // LARAVEL VALIDATION
        $validation = [
            'login_id' => 'required',
            'login_pass' => 'required'
        ];
        $message = [
            'required' => ':attribute ' . lang('should not be empty', $this->translation)
        ];
        $names = [
            'login_id' => ucwords(lang('username', $this->translation)),
            'login_pass' => ucwords(lang('password', $this->translation))
        ];
        $this->validate($request, $validation, $message, $names);

        if (env('RECAPTCHA_SECRET_KEY_ADMIN')) {
            // reCAPTCHA checking...
            $recaptcha = Helper::validate_recaptcha($request->input('g-recaptcha-response'), env('RECAPTCHA_SECRET_KEY_ADMIN'));
            if (!$recaptcha) {
                // reCAPTCHA FAILED
                return back()->withInput()->with('error', lang('reCAPTCHA validation unsuccessful, please try again', $this->translation));
            }
        }

        $password = Helper::hashing_this($request->login_pass);

        // GET THE DATA
        $admin = SysUser::select(
            'sys_users.id',
            'sys_users.name',
            'sys_users.username',
            'sys_users.force_logout',
            'sys_users.status',
            'sys_groups.id as group_id',
            'sys_groups.name as group_name'
        )
            ->leftJoin('sys_user_group', 'sys_users.id', '=', 'sys_user_group.user')
            ->leftJoin('sys_groups', 'sys_user_group.group', '=', 'sys_groups.id')
            ->where([
                'username' => Helper::validate_input($request->login_id),
                'password' => $password
            ])
            ->first();

        // CHECK IS DATA EXIST
        if ($admin) {
            if ($admin->status != 1) {
                return back()
                    ->withInput()
                    ->with('error', lang('Login failed! Because your account has been disabled!', $this->translation));
            }

            // UPDATE "FORCE LOGOUT" STATUS
            if ($admin->force_logout) {
                $admin->force_logout = 0;
                $admin->save();
            }

            // SUCCESS LOGIN
            // LOGGING
            $log = new SysLog();
            $log->subject = $admin->id;
            $log->action = 1;
            $log->save();

            // GET USER'S ACCESS
            $access = [];
            $get_access = SysGroupRule::select(
                'sys_group_rule.rule_id',
                'sys_rules.name as rule_name',
                'sys_rules.description as rule_desc',
                'sys_modules.name as module_name'
            )
                ->leftJoin('sys_rules', 'sys_rules.id', 'sys_group_rule.rule_id')
                ->leftJoin('sys_modules', 'sys_rules.module_id', 'sys_modules.id')
                ->where('sys_group_rule.group_id', $admin->group_id)
                ->where('sys_modules.status', 1)
                ->get();
            if (count($get_access) > 0) {
                foreach ($get_access as $item) {
                    $obj = new \stdClass();
                    $obj->rule_id = $item->rule_id;
                    $obj->rule_name = $item->rule_name;
                    $obj->rule_desc = $item->rule_desc;
                    $obj->module_name = $item->module_name;
                    $access[] = $obj;
                }
            }

            // GET USER'S ACCESS DIVISIONS & BRANCHES
            $division_allowed = [];
            $branch_allowed = [];
            $get_branch_allowed = SysGroupBranch::select(
                'sys_branches.*',
                'sys_divisions.name as division_name',
                'sys_divisions.id as division_id'
            )
                ->leftJoin('sys_branches', 'sys_group_branch.branch', '=', 'sys_branches.id')
                ->leftJoin('sys_divisions', 'sys_branches.division_id', '=', 'sys_divisions.id')
                ->whereNull('sys_branches.deleted_at')
                ->where('sys_group_branch.group', $admin->group_id)
                ->orderBy('sys_divisions.name')
                ->orderBy('sys_branches.name')
                ->get();
            if (count($get_branch_allowed) > 0) {
                foreach ($get_branch_allowed as $item) {
                    $obj = new \stdClass();
                    $obj->branch_id = $item->id;
                    $obj->branch = $item->name;
                    $obj->division_id = $item->division_id;
                    $obj->division = $item->division_name;
                    $branch_allowed[] = $obj;

                    if (!in_array($item->division_name, $division_allowed)) {
                        $division_allowed[] = $item->division_name;
                    }
                }
            }

            // SET REDIRECT URI FROM SESSION (IF ANY)
            $redirect_uri = route('admin.home');
            if (Session::has('redirect_uri')) {
                $redirect_uri = Session::get('redirect_uri');
            }

            return redirect($redirect_uri)
                ->with(Session::put('admin', $admin))
                ->with(Session::put('access', $access))
                ->with(Session::put('branch', $branch_allowed))
                ->with(Session::put('division', $division_allowed))
                ->with(Session::put('auth', Helper::generate_token($password)));
        }

        // FAILED
        return back()
            ->withInput()
            ->with('error', lang('Username or Password is wrong!', $this->translation));
    }

    public function logout()
    {
        $session = Session::get('admin');
        if (isset($session)) {
            // LOGGING
            $log = new SysLog();
            $log->subject = $session->id;
            $log->action = 2;
            $log->save();
        }

        Session::flush();
        return redirect()
            ->route('admin.login')
            ->with('success', lang('Logout successfully', $this->translation));
    }

    public function logout_all()
    {
        $message = 'Logout successfully';

        $session = Session::get('admin');
        if (isset($session)) {
            // LOGGING
            $log = new SysLog();
            $log->subject = $session->id;
            $log->action = 2;
            $log->save();

            $message = 'Logout from all sessions successfully';
            $user = SysUser::find($session->id);
            $user->force_logout = 1;
            $user->save();
        }

        Session::flush();
        return redirect()
            ->route('admin.login')
            ->with('success', lang($message, $this->translation));
    }

    private function is_provider_allowed($driver)
    {
        return in_array($driver, $this->providers) && config()->has("services.{$driver}");
    }

    protected function send_failed_response($msg = null)
    {
        return redirect()
            ->route('admin.login')
            ->withErrors(['msg' => $msg ?: 'Unable to login, try with another provider to login.']);
    }

    public function redirect_to_provider($driver)
    {
        if (!$this->is_provider_allowed($driver)) {
            return $this->send_failed_response("{$driver} is not currently supported");
        }

        try {
            if ($driver == 'instagram') {
                $appId = env('INSTAGRAM_CLIENT_ID');
                $redirectUri = urlencode(env('INSTAGRAM_CALLBACK_URL'));
                return redirect()->to("https://api.instagram.com/oauth/authorize?client_id={$appId}&redirect_uri={$redirectUri}&scope=user_profile,user_media&response_type=code");
            } else {
                return Socialite::driver($driver)->redirect();
            }
        } catch (Exception $e) {
            // You should show something simple fail message
            return $this->send_failed_response($e->getMessage());
        }
    }

    public function handle_provider_callback($social, Request $request)
    {
        if ($social == 'instagram') {
            // https://stackoverflow.com/questions/59142407/laravel-integrate-with-instagram-api-after-october-2019
            // https://developers.facebook.com/docs/instagram-basic-display-api/getting-started
            $code = $request->code;
            if (empty($code)) {
                return redirect()
                    ->route('admin.login')
                    ->with('error', 'Authentication with ' . ucwords($social) . ' failed, please try again.');
            }

            $appId = env('INSTAGRAM_CLIENT_ID');
            $secret = env('INSTAGRAM_CLIENT_SECRET');
            $redirectUri = env('INSTAGRAM_CALLBACK_URL');

            // Set API URL - retrieve the data (Get access token)
            $url = 'https://api.instagram.com/oauth/access_token';
            // Set parameters
            $params = [
                'app_id' => $appId,
                'app_secret' => $secret,
                'grant_type' => 'authorization_code',
                'redirect_uri' => $redirectUri,
                'code' => $code
            ];
            // Hit API - using method POST
            $response = $this->guzzle_post_public($url, $params);

            if (isset($response->code)) {
                return redirect()
                    ->route('admin.login')
                    ->with('error', 'Unauthorized login to Instagram');
            }

            // get access token for exchange it for a token
            $accessToken = $response->access_token;

            // Set API URL - retrieve the data (Get user info)
            $url = "https://graph.instagram.com/me?fields=id,username,account_type,media_count&access_token={$accessToken}";
            // Hit API - using method GET
            $response = $this->guzzle_get_public($url);
            $oAuth = $response;
            
            // *dumping $oAuth
            // {#448 ▼
            //   +"id": "17841401572570570"
            //   +"username": "vickzkater"
            //   +"account_type": "PERSONAL"
            // }

            dd($oAuth);
        } else {
            $user = Socialite::driver($social)->user();

            if ($user) {
                $admin = SysUser::select(
                    'sys_users.id',
                    'sys_users.name',
                    'sys_users.username',
                    'sys_users.password',
                    'sys_users.status',
                    'sys_groups.id as group_id',
                    'sys_groups.name as group_name'
                )
                    ->leftJoin('sys_user_group', 'sys_users.id', '=', 'sys_user_group.user')
                    ->leftJoin('sys_groups', 'sys_user_group.group', '=', 'sys_groups.id')
                    ->where('sys_users.email', $user->email)
                    ->first();

                if (!$admin) {
                    // USER NOT FOUND, SO CREATE NEW USER DATA 
                    $admin = new SysUser();
                    $admin->name = $user->name;
                    $admin->username = Helper::random_string();
                    $admin->email = $user->email;
                    $admin->email_verified_at = date('Y-m-d H:i:s');
                    $admin->password = Helper::hashing_this(Helper::random_string());
                    // $admin->$social_id = $user->id;

                    if (!$admin->save()) {
                        // FAILED
                        return redirect()
                            ->route('admin.login')
                            ->with('error', 'Failed to register new user, please try again.');
                    }

                    $access = [];
                    $division_allowed = [];
                    $branch_allowed = [];
                } else {
                    // REGISTERED
                    if ($admin->status != 1) {
                        return back()
                            ->withInput()
                            ->with('error', lang('Login failed! Because your account has been disabled!', $this->translation));
                    }

                    // GET USER'S ACCESS
                    $access = [];
                    $get_access = SysGroupRule::select(
                        'sys_group_rule.rule_id',
                        'sys_rules.name as rule_name',
                        'sys_rules.description as rule_desc',
                        'sys_modules.name as module_name'
                    )
                        ->leftJoin('sys_rules', 'sys_rules.id', 'sys_group_rule.rule_id')
                        ->leftJoin('sys_modules', 'sys_rules.module_id', 'sys_modules.id')
                        ->where('sys_group_rule.group_id', $admin->group_id)
                        ->where('sys_modules.status', 1)
                        ->get();
                    if (count($get_access) > 0) {
                        foreach ($get_access as $item) {
                            $obj = new \stdClass();
                            $obj->rule_id = $item->rule_id;
                            $obj->rule_name = $item->rule_name;
                            $obj->rule_desc = $item->rule_desc;
                            $obj->module_name = $item->module_name;
                            $access[] = $obj;
                        }
                    }

                    // GET USER'S ACCESS DIVISIONS & BRANCHES
                    $division_allowed = [];
                    $branch_allowed = [];
                    $get_branch_allowed = SysGroupBranch::select(
                        'sys_branches.*',
                        'sys_divisions.name as division_name',
                        'sys_divisions.id as division_id'
                    )
                        ->leftJoin('sys_branches', 'sys_group_branch.branch', '=', 'sys_branches.id')
                        ->leftJoin('sys_divisions', 'sys_branches.division_id', '=', 'sys_divisions.id')
                        ->whereNull('sys_branches.deleted_at')
                        ->where('sys_group_branch.group', $admin->group_id)
                        ->orderBy('sys_divisions.name')
                        ->orderBy('sys_branches.name')
                        ->get();
                    if (count($get_branch_allowed) > 0) {
                        foreach ($get_branch_allowed as $item) {
                            $obj = new \stdClass();
                            $obj->branch_id = $item->id;
                            $obj->branch = $item->name;
                            $obj->division_id = $item->division_id;
                            $obj->division = $item->division_name;
                            $branch_allowed[] = $obj;

                            if (!in_array($item->division_name, $division_allowed)) {
                                $division_allowed[] = $item->division_name;
                            }
                        }
                    }
                }

                $password = $admin->password;

                // LOGGING
                $log = new SysLog();
                $log->subject = $admin->id;
                $log->action = 1;
                $log->save();

                // SET REDIRECT URI FROM SESSION (IF ANY)
                $redirect_uri = route('admin.home');
                if (Session::has('redirect_uri')) {
                    $redirect_uri = Session::get('redirect_uri');
                }

                // SUCCESS
                return redirect($redirect_uri)
                    ->with(Session::put('admin', $admin))
                    ->with(Session::put('access', $access))
                    ->with(Session::put('branch', $branch_allowed))
                    ->with(Session::put('division', $division_allowed))
                    ->with(Session::put('auth', Helper::generate_token($password)));
            }
        }

        // FAILED
        return redirect()
            ->route('admin.login')
            ->with('error', 'Authentication with ' . ucwords($social) . ' failed, please try again.');
    }
}
