<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

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
            // get default language
            $default_lang = env('DEFAULT_LANGUAGE', 'EN');

            // set language
            $language = Session::get('language');
            if (empty($language)) {
                $language = $default_lang;
                Session::put('language', $language);
            }

            // get language data
            $getLanguageMasterMenu = DB::table('sys_language_master_details')
                ->select('sys_language_master.phrase', 'sys_language_master_details.translate')
                ->leftJoin('sys_languages', 'sys_language_master_details.language_id', '=', 'sys_languages.id')
                ->leftJoin('sys_language_master', 'sys_language_master_details.language_master_id', '=', 'sys_language_master.id')
                ->where('sys_languages.alias', $language)
                ->get();

            // convert to single array
            $translation = [];
            foreach ($getLanguageMasterMenu as $list) {
                $translation[$list->phrase] = $list->translate;
            }

            // share variable to all Views
            View::share('translation', $translation);

            // set this variable with translation data
            $this->translation = $translation;

            // set available languages
            $getLanguages = DB::table('sys_languages')->where('status', 1)->get();

            // convert to single array
            $languages = [];
            foreach ($getLanguages as $list) {
                $obj = new \stdClass();
                $obj->alias = $list->alias;
                $obj->name = $list->name;
                $languages[$list->id] = $obj;
            }

            // share variable to all Views
            View::share('languages', $languages);

            // set this variable with languages data
            $this->languages = $languages;

            // get global config data
            $global_config = DB::table('sys_config')->first();

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
}
