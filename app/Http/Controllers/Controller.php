<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use GuzzleHttp\Client;

// LIBRARIES
use App\Libraries\Helper;

// MODELS
use App\Models\system\SysUser;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    // variable for save the translation
    public $translation;
    // variable for save available laguages
    public $languages;
    // variable for save application logo
    public $app_logo;
    // variable for save global config
    public $global_config;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            
            if (Session::has('admin')) {
                // GET USER DATA
                $user_session = Session::get('admin');
                $user_data = SysUser::find($user_session->id);
                
                // if password has been changed, then force user to re-login
				$auth = Helper::generate_token($user_data->password);
				$token_db = Helper::validate_token($auth);
                $token_session = Helper::validate_token(Session::get('auth'));
				if ($token_db != $token_session) {
					// PASSWORD HAS BEEN CHANGED, THEN FORCE USER TO RE-LOGIN
					Session::flush();
                    return redirect()->route('admin.login')->with('info', lang('Your password has been changed, please re-login'));
                }
                
                if($user_data->force_logout){
                    // FORCE LOGOUT FROM ALL SESSIONS
					Session::flush();
                    return redirect()->route('admin.login')->with('info', lang('Your session has been logged out, please re-login'));
                }
			}

            // get default language
            $default_lang = env('DEFAULT_LANGUAGE', 'EN');

            // set language
            $language = Session::get('language');
            if (empty($language)) {
                $language = $default_lang;
                Session::put('language', $language);
            }

            // get language data
            $translation = [];
            if (env('APP_BACKEND', 'MODEL') != 'API' && env('MULTILANG_MODULE', false)) {
                $getLanguageMasterMenu = DB::table('sys_language_master_details')
                    ->select('sys_language_master.phrase', 'sys_language_master_details.translate')
                    ->leftJoin('sys_languages', 'sys_language_master_details.language_id', '=', 'sys_languages.id')
                    ->leftJoin('sys_language_master', 'sys_language_master_details.language_master_id', '=', 'sys_language_master.id')
                    ->where('sys_languages.alias', $language)
                    ->get();

                // convert to single array
                foreach ($getLanguageMasterMenu as $list) {
                    $translation[$list->phrase] = $list->translate;
                }
            }
            // share variable to all Views
            View::share('translation', $translation);
            // set this variable with translation data
            $this->translation = $translation;

            // set available languages
            $languages = [];
            if (env('APP_BACKEND', 'MODEL') != 'API' && env('MULTILANG_MODULE', false)) {
                $getLanguages = DB::table('sys_languages')->where('status', 1)->get();

                // convert to single array
                foreach ($getLanguages as $list) {
                    $obj = new \stdClass();
                    $obj->alias = $list->alias;
                    $obj->name = $list->name;
                    $languages[$list->id] = $obj;
                }
            }
            // share variable to all Views
            View::share('languages', $languages);
            // set this variable with languages data
            $this->languages = $languages;

            // get global config data
            $global_config = new \stdClass();
            $global_config->app_name = env('APP_NAME');
            $global_config->app_version = env('APP_VERSION');
            $global_config->app_url_site = env('APP_URL_SITE');
            $global_config->app_favicon_type = env('APP_FAVICON_TYPE');
            $global_config->app_favicon = env('APP_FAVICON');
            $global_config->app_logo = env('APP_LOGO');
            $global_config->app_logo_image = env('APP_LOGO_IMAGE');
            $global_config->powered = env('POWERED');
            $global_config->powered_url = env('POWERED_URL');

            $global_config->meta_title = env('APP_NAME');
            $global_config->meta_description = env('META_DESCRIPTION');
            $global_config->meta_author = env('META_AUTHOR');
            $global_config->meta_keywords = '';
            
            if (env('APP_BACKEND', 'MODEL') != 'API') {
                $global_config = DB::table('sys_config')->first();
            }
            // share variable to all Views
            View::share('global_config', $global_config);
            // set this variable with translation data
            $this->global_config = $global_config;

            // set app logo
            if (empty($global_config->app_logo_image)) {
                $app_logo = '<i class="fa fa-' . $global_config->app_logo . '"></i>';
            } else {
                $app_logo = '<img src=" ' . asset($global_config->app_logo_image) . '" style="max-width:40px" />';
            }

            // share variable to all Views
            View::share('app_logo', $app_logo);

            // set this variable with translation data
            $this->app_logo = $app_logo;

            return $next($request);
        });
    }

    /**
     * Guzzle GET Public Access (without token Authorization Bearer)
     * 
     * @param String $url (Target API URL) required
     * @param Array $auth (htaccess auth) optional
     * 
     * @return Object (API Response)
     */
    protected function guzzle_get_public($url, $auth = null)
    {
        $config = ['http_errors' => false];
        if (env('APP_DEBUG')) {
            $config['verify'] = false;
        }
        if ($auth) {
            $config['auth'] = $auth;
        }
        $client = new Client($config);
        $request = $client->request('GET', $url);
        $response = $request->getBody();

        return json_decode($response);
    }

    /**
     * Guzzle POST Public Access (without token Authorization Bearer)
     * 
     * @param String $url (Target API URL) required
     * @param Array $parameter (Paramaters) required
     * @param Array $auth (htaccess auth) optional
     * 
     * @return Object (API Response)
     */
    protected function guzzle_post_public($url, $parameter, $auth = null)
    {
        $config = ['http_errors' => false];
        if (env('APP_DEBUG')) {
            $config['verify'] = false;
        }
        if ($auth) {
            $config['auth'] = $auth;
        }
        $client = new Client($config);
        $request = $client->request('POST', $url, ['form_params' => $parameter]);
        $response = $request->getBody()->getContents();

        return json_decode($response);
    }

    /**
     * Guzzle GET with token Authorization Bearer
     * 
     * @param String $url (Target API URL) required
     * @param String $token (Token Authorization Bearer) required
     * @param Array $auth (htaccess auth) optional
     * 
     * @return Object (API Response)
     */
    protected function guzzle_get($url, $token, $auth = null)
    {
        if (empty($token)) {
            return 'Unauthorized';
        } else {
            $config = ['http_errors' => false];
            if (env('APP_DEBUG')) {
                $config['verify'] = false;
            }
            if ($auth) {
                $config['auth'] = $auth;
            }
            $client = new Client($config);
            $headers = array('Authorization' => 'bearer ' . $token);
            $request = $client->request('GET', $url, array('headers' => $headers));
            $response = $request->getBody();

            return json_decode($response);
        }
    }

    /**
     * Guzzle POST with token Authorization Bearer
     * 
     * @param String $url (Target API URL) required
     * @param String $token (Token Authorization Bearer) required
     * @param Array $parameter (Paramaters) required
     * @param Array $auth (htaccess auth) optional
     * 
     * @return Object (API Response)
     */
    protected function guzzle_post($url, $token, $parameter, $auth = null)
    {
        if (empty($token)) {
            return 'Unauthorized';
        } else {
            $config = ['http_errors' => false];
            if (env('APP_DEBUG')) {
                $config['verify'] = false;
            }
            if ($auth) {
                $config['auth'] = $auth;
            }
            $client = new Client($config);
            $headers = array('Authorization' => 'bearer ' . $token);
            $request = $client->request('POST', $url, ['headers' => $headers, 'form_params' => $parameter]);
            $response = $request->getBody()->getContents();

            return json_decode($response);
        }
    }

    /**
     * Guzzle POST file with token Authorization Bearer
     * 
     * @param String $url (Target API URL) required
     * @param String $token (Token Authorization Bearer) required
     * @param Array $parameter (Paramaters) required
     * @param Array $auth (htaccess auth) optional
     * 
     * @return Object (API Response)
     */
    // *Sample:
    // // Set params
    // $params = [
    //     [
    //         'name'     => 'user_id',
    //         'contents' => $user_id
    //     ],
    //     [
    //         'name'     => 'avatar',
    //         'contents' => fopen($request->file('avatar')->getRealPath(), "r"),
    //         'filename' => $request->file('avatar')->hashName()
    //     ]
    // ];
    protected function guzzle_post_multipart($url, $token, $parameter, $auth = null)
    {
        if (empty($token)) {
            return 'Unauthorized';
        } else {
            $config = ['http_errors' => false];
            if (env('APP_DEBUG')) {
                $config['verify'] = false;
            }
            if ($auth) {
                $config['auth'] = $auth;
            }
            $client = new Client($config);
            $headers = array('Authorization' => 'bearer ' . $token);
            $request = $client->request('POST', $url, ['headers' => $headers, 'multipart' => $parameter]);
            $response = $request->getBody()->getContents();

            return json_decode($response);
        }
    }
}
