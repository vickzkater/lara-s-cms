<?php

namespace App\Libraries;

use Illuminate\Support\Facades\Session;

// MODELS
use App\Models\system\SysLog;

class Helper extends TheHelper
{
    public static function is_superadmin()
    {
        // special access for "Super Administrator" group
        $admin = Session::get('admin');
        if ($admin->group_id == 1) {
            return true;
        }
        return false;
    }

    public static function authorizing($module_name, $rule_name)
    {
        // special access for "Super Administrator" group
        $admin = Session::get('admin');
        if ($admin->group_id == 1) {
            return ['status' => 'true'];
        }

        if (empty($module_name) || empty($rule_name)) {
            return ['status' => 'false', 'message' => 'Sorry, you are unauthorized'];
        }

        // get access from session
        $access = Session::get('access');

        $granted = false;
        foreach ($access as $item) {
            if ($item->module_name == $module_name && $item->rule_name == $rule_name) {
                $granted = true;
                break;
            }
        }

        if ($granted) {
            return ['status' => 'true'];
        }

        // UNAUTHORIZED...
        return ['status' => 'false', 'message' => 'Sorry, you are  unauthorized for access ' . $rule_name . ' in ' . $module_name];
    }

    public static function authorizing_division($division_name)
    {
        // special access for "Super Administrator" group
        $admin = Session::get('admin');
        if ($admin->group_id == 1) {
            return ['status' => 'true', 'message' => 'all'];
        }

        if (empty($division_name)) {
            return ['status' => 'false', 'message' => 'Sorry, you are unauthorized'];
        }

        return ['status' => 'true', 'message' => $division_name];
    }

    public static function logging($action, $object = null)
    {
        $log = new SysLog();
        $log->subject = Session::get('admin')->id;
        $log->action = $action;
        if (!empty($object)) {
            $log->object = $object;
        }

        $log->save();
    }

    public static function get_authorized_branches()
    {
        // special access for "Super Administrator" group
        $admin = Session::get('admin');
        if ($admin->group_id == 1) {
            return 'all';
        }

        $allowed_divisions = [];
        $allowed_branches = [];
        $allowed_div_branch_name = [];

        $sessions = Session::all();
        foreach ($sessions['branch'] as $item) {
            $allowed_divisions[] = $item->division_id;
            $allowed_branches[] = $item->branch_id;
            $allowed_div_branch_name[$item->branch_id] = $item->division . ' - ' . $item->branch;
        }

        return [
            'status' => 'true',
            'allowed_divisions' => $allowed_divisions,
            'allowed_branches' => $allowed_branches,
            'allowed_div_branch_name' => $allowed_div_branch_name
        ];
    }

    public static function get_periods($translation)
    {
        return array(lang('second', $translation), lang('minute', $translation), lang('hour', $translation), lang('day', $translation), lang('week', $translation), lang('month', $translation), lang('year', $translation), lang('decade', $translation));
    }

    public static function upload_image($dir_path, $image_file, $reformat_image_name = true, $format_image_name = null)
    {
        // PROCESSING IMAGE
        $destination_path = public_path($dir_path);
        $image = $image_file;
        $extension  = strtolower($image->getClientOriginalExtension());
        if ($reformat_image_name) {
            // REFORMAT IMAGE NAME USING $format_image_name
            if ($format_image_name) {
                $image_name = $format_image_name . '.' . $extension;
            } else {
                // REFORMAT IMAGE NAME USING TIMESTAMP
                $image_name = time() . '.' . $extension;
            }
        }
        // UPLOADING...
        if (!$image->move($destination_path, $image_name)) {
            // FAILED
            return false;
        }
        // SUCCESS
        return $image_name;
    }
}
