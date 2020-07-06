<?php

namespace App\Libraries;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

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

    public static function upload_image($dir_path, $image_file, $reformat_image_name = true, $format_image_name = null, $allowed_extensions = ['jpeg', 'jpg', 'png', 'gif'])
    {
        // PROCESSING IMAGE
        $destination_path = public_path($dir_path);
        $image = $image_file;
        $extension = strtolower($image->getClientOriginalExtension());

        // VALIDATING FOR ALLOWED EXTENSIONS
        if (!in_array($extension, $allowed_extensions)) {
            // FAILED
            return [
                'status' => 'false',
                'message' => 'Failed to upload the image, please upload image with allowed extensions (' . implode(",", $allowed_extensions) . ')'
            ];
        }

        if ($reformat_image_name) {
            // REFORMAT IMAGE NAME USING $format_image_name
            if ($format_image_name) {
                $image_name = $format_image_name . '.' . $extension;
            } else {
                // REFORMAT IMAGE NAME USING TIMESTAMP
                $image_name = time() . '.' . $extension;
            }
        } else {
            // USING ORIGINAL FILENAME
            $image_name = $image->getClientOriginalName();
        }

        // UPLOADING...
        if (!$image->move($destination_path, $image_name)) {
            // FAILED
            return [
                'status' => 'false',
                'message' => 'Oops, failed to upload image. Please try again or try upload another one.'
            ];
        }

        // SUCCESS
        return [
            'status' => 'true',
            'message' => 'Successfully uploaded the image',
            'data' => $image_name
        ];
    }

    /**
     * Validate & generate unique slug
     * 
     * @param String $table_name required | table name
     * @param String $slug required | input text
     * @param String $table_name optional | field name in table
     * 
     * @return String unique slug
     */
    public static function check_slug($table_name, $slug, $field_name = 'slug')
    {
        $unique = false;
        $no = 2;
        $slug_raw = $slug;
        while (!$unique) {
            $slug_exist = DB::table($table_name)->where($field_name, $slug)->count();
            if ($slug_exist == 0) {
                $unique = true;
            } else {
                // SET NEW SLUG
                $slug = $slug_raw . '-' . $no;
                $no++;
            }
        }
        return $slug;
    }

    public static function upload_file($dir_path, $file, $reformat_file_name = true, $format_file_name = null, $allowed_extensions = ['pdf', 'txt', 'docx', 'doc'])
    {
        // PROCESSING FILE
        $destination_path = public_path($dir_path);
        $extension = strtolower($file->getClientOriginalExtension());

        // VALIDATING FOR ALLOWED EXTENSIONS
        if (!in_array($extension, $allowed_extensions)) {
            // FAILED
            return [
                'status' => 'false',
                'message' => 'Failed to upload the file, please upload file with allowed extensions (' . implode(",", $allowed_extensions) . ')'
            ];
        }

        if ($reformat_file_name) {
            // REFORMAT FILE NAME USING $format_file_name
            if ($format_file_name) {
                $file_name = $format_file_name . '.' . $extension;
            } else {
                // REFORMAT FILE NAME USING TIMESTAMP
                $file_name = time() . '.' . $extension;
            }
        } else {
            // USING ORIGINAL FILENAME
            $file_name = $file->getClientOriginalName();
        }

        // UPLOADING...
        if (!$file->move($destination_path, $file_name)) {
            // FAILED
            return [
                'status' => 'false',
                'message' => 'Oops, failed to upload file. Please try again or try upload another one.'
            ];
        }

        // SUCCESS
        return [
            'status' => 'true',
            'message' => 'Successfully uploaded the file',
            'data' => $file_name
        ];
    }
}
