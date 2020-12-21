<?php

namespace App\Http\Controllers\Admin\system;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// LIBRARIES
use App\Libraries\Helper;

// MODELS
use App\Models\system\SysConfig;

class ConfigController extends Controller
{
    // SET THIS MODULE
    private $module = 'Config';
    // SET THIS OBJECT/ITEM NAME
    private $item = 'config';

    public function view()
    {
        $data = SysConfig::first();

        return view('admin.system.config.form', compact('data'));
    }

    public function update(Request $request)
    {
        // AUTHORIZING...
        $authorize = Helper::authorizing($this->module, 'Update');
        if ($authorize['status'] != 'true') {
            return back()->with('error', $authorize['message']);
        }

        // SET THIS OBJECT/ITEM NAME BASED ON TRANSLATION
        $this->item = ucwords(lang($this->item, $this->translation));

        // GET THE DATA BASED ON ID
        $data = SysConfig::find(1);

        // CHECK IS DATA FOUND
        if (!$data) {
            // DATA NOT FOUND
            return back()
                ->withInput()
                ->with('error', lang('#item not found, please check the database', $this->translation, ['#item' => $this->item]));
        }

        // LARAVEL VALIDATION
        $validation = [
            'app_name' => 'required',
            'app_url_site' => 'required',
            'app_version' => 'required',
            'app_favicon_type' => 'required',
            'app_logo' => 'required',
            'help' => 'required',
            'meta_keywords' => 'required',
            'meta_title' => 'required',
            'meta_description' => 'required',
            'meta_author' => 'required'
        ];
        $message = [
            'required' => ':attribute ' . lang('field is required', $this->translation)
        ];
        $names = [
            'app_name' => ucwords(lang('application name', $this->translation)),
            'app_url_site' => ucwords(lang('application URL', $this->translation)),
            'app_version' => ucwords(lang('application version', $this->translation)),
            'app_favicon_type' => ucwords(lang('favicon type', $this->translation)),
            'app_logo' => ucwords(lang('application logo', $this->translation)),
            'help' => ucwords(lang('help', $this->translation)),
            'meta_keywords' => ucwords(lang('meta keywords', $this->translation)),
            'meta_title' => ucwords(lang('meta title', $this->translation)),
            'meta_description' => ucwords(lang('meta description', $this->translation)),
            'meta_author' => ucwords(lang('meta author', $this->translation))
        ];
        $this->validate($request, $validation, $message, $names);

        // HELPER VALIDATION FOR PREVENT SQL INJECTION & XSS ATTACK
        $app_name = Helper::validate_input_text($request->app_name);
        if (!$app_name) {
            return back()
                ->withInput()
                ->with('error', lang('Invalid format for #item', $this->translation, ['#item' => ucwords(lang('application name', $this->translation))]));
        }
        $data->app_name = $app_name;

        $app_url_site = Helper::validate_input_url($request->app_url_site);
        if (!$app_url_site) {
            return back()
                ->withInput()
                ->with('error', lang('Invalid format for #item', $this->translation, ['#item' => ucwords(lang('application URL', $this->translation))]));
        }
        $data->app_url_site = $app_url_site;

        $data->app_url_main = Helper::validate_input_url($request->app_url_main);

        $app_version = Helper::validate_input_text($request->app_version);
        if (!$app_version) {
            return back()
                ->withInput()
                ->with('error', lang('Invalid format for #item', $this->translation, ['#item' => ucwords(lang('application version', $this->translation))]));
        }
        $data->app_version = $app_version;

        $app_favicon_type = Helper::validate_input_text($request->app_favicon_type);
        if (!$app_favicon_type) {
            return back()
                ->withInput()
                ->with('error', lang('Invalid format for #item', $this->translation, ['#item' => ucwords(lang('favicon type', $this->translation))]));
        }
        $app_favicon_type = strtolower($app_favicon_type);
        if (!in_array($app_favicon_type, ['ico', 'png', 'jpg', 'jpeg'])) {
            return back()
                ->withInput()
                ->with('error', lang('#item only support ico/png/jpg/jpeg', $this->translation, ['#item' => ucwords(lang('favicon type', $this->translation))]));
        }
        $data->app_favicon_type = $app_favicon_type;

        // IF UPLOAD NEW IMAGE
        if ($request->app_favicon) {
            // PROCESSING IMAGE
            $dir_path = 'uploads/config/';
            $image_file = $request->file('app_favicon');
            $format_image_name = 'favicon-' . time();
            $image = Helper::upload_image($dir_path, $image_file, true, $format_image_name, ['ico', 'png', 'jpg', 'jpeg']);
            if ($image['status'] != 'true') {
                return back()
                    ->withInput()
                    ->with('error', lang($image['message'], $this->translation, $image['dynamic_objects']));
            }
            // GET THE UPLOADED IMAGE RESULT
            $data->app_favicon = $dir_path . $image['data'];
        }

        $app_logo = Helper::validate_input_text($request->app_logo);
        if (!$app_logo) {
            return back()
                ->withInput()
                ->with('error', lang('Invalid format for #item', $this->translation, ['#item' => ucwords(lang('application logo', $this->translation))]));
        }
        $data->app_logo = $app_logo;

        // IF UPLOAD NEW IMAGE
        if ($request->app_logo_image) {
            // PROCESSING IMAGE
            $dir_path = 'uploads/config/';
            $image_file = $request->file('app_logo_image');
            $format_image_name = Helper::generate_slug($app_name) . '-' . time();
            $image = Helper::upload_image($dir_path, $image_file, true, $format_image_name);
            if ($image['status'] != 'true') {
                return back()
                    ->withInput()
                    ->with('error', lang($image['message'], $this->translation, $image['dynamic_objects']));
            }
            // GET THE UPLOADED IMAGE RESULT
            $data->app_logo_image = $dir_path . $image['data'];
        }

        $help = Helper::validate_input_text($request->help);
        if (!$help) {
            return back()
                ->withInput()
                ->with('error', lang('Invalid format for #item', $this->translation, ['#item' => ucwords(lang('help', $this->translation))]));
        }
        $data->help = $help;

        $data->powered = Helper::validate_input_text($request->powered);

        if ($request->powered_url) {
            $powered_url = Helper::validate_input_url($request->powered_url);
            if (!$powered_url) {
                return back()
                    ->withInput()
                    ->with('error', lang('Invalid format for #item', $this->translation, ['#item' => (lang('Powered URL', $this->translation))]));
            }
            $data->powered_url = $powered_url;
        } else {
            $data->powered_url = null;
        }

        if ($request->meta_keywords) {
            $meta_keywords = Helper::validate_input_text($request->meta_keywords);
            if (!$meta_keywords) {
                return back()
                    ->withInput()
                    ->with('error', lang('Invalid format for #item', $this->translation, ['#item' => ucwords(lang('meta keywords', $this->translation))]));
            }
            $data->meta_keywords = $meta_keywords;
        } else {
            $data->meta_keywords = null;
        }

        $meta_title = Helper::validate_input_text($request->meta_title);
        if (!$meta_title) {
            return back()
                ->withInput()
                ->with('error', lang('Invalid format for #item', $this->translation, ['#item' => ucwords(lang('meta title', $this->translation))]));
        }
        $data->meta_title = $meta_title;

        $meta_description = Helper::validate_input_text($request->meta_description);
        if (!$meta_description) {
            return back()
                ->withInput()
                ->with('error', lang('Invalid format for #item', $this->translation, ['#item' => ucwords(lang('meta description', $this->translation))]));
        }
        $data->meta_description = $meta_description;

        $meta_author = Helper::validate_input_text($request->meta_author);
        if (!$meta_author) {
            return back()
                ->withInput()
                ->with('error', lang('Invalid format for #item', $this->translation, ['#item' => ucwords(lang('meta author', $this->translation))]));
        }
        $data->meta_author = $meta_author;

        // Open Graph
        $og_type = Helper::validate_input_text($request->og_type);
        $data->og_type = $og_type;
        $og_site_name = Helper::validate_input_text($request->og_site_name);
        $data->og_site_name = $og_site_name;
        $og_title = Helper::validate_input_text($request->og_title);
        $data->og_title = $og_title;
        // IF UPLOAD NEW IMAGE
        if ($request->og_image) {
            // PROCESSING IMAGE
            $dir_path = 'uploads/config/';
            $image_file = $request->file('og_image');
            $format_image_name = Helper::generate_slug($app_name) . '-og-' . time();
            $image = Helper::upload_image($dir_path, $image_file, true, $format_image_name);
            if ($image['status'] != 'true') {
                return back()
                    ->withInput()
                    ->with('error', lang($image['message'], $this->translation, $image['dynamic_objects']));
            }
            // GET THE UPLOADED IMAGE RESULT
            $data->og_image = $dir_path . $image['data'];
        }
        $og_description = Helper::validate_input_text($request->og_description);
        $data->og_description = $og_description;

        // Twitter OG
        $twitter_card = Helper::validate_input_text($request->twitter_card);
        $data->twitter_card = $twitter_card;
        $twitter_site = Helper::validate_input_text($request->twitter_site);
        $data->twitter_site = $twitter_site;
        $twitter_site_id = Helper::validate_input_text($request->twitter_site_id);
        $data->twitter_site_id = $twitter_site_id;
        $twitter_creator = Helper::validate_input_text($request->twitter_creator);
        $data->twitter_creator = $twitter_creator;
        $twitter_creator_id = Helper::validate_input_text($request->twitter_creator_id);
        $data->twitter_creator_id = $twitter_creator_id;

        // FB
        $fb_app_id = Helper::validate_input_text($request->fb_app_id);
        $data->fb_app_id = $fb_app_id;

        if ($data->save()) {
            // SUCCESS
            return redirect()
                ->route('admin.config')
                ->with('success', lang('Successfully updated #item', $this->translation, ['#item' => $this->item]));
        }

        // FAILED
        return back()
            ->withInput()
            ->with('error', lang('Oops, failed to update #item. Please try again.', $this->translation, ['#item' => $this->item]));
    }
}
