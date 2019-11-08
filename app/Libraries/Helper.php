<?php
namespace App\Libraries;

use Session;

use App\Models\Rule;
use App\Models\Product;

class Helper
{
	/*
	 * for validating input - prevent SQL injection & XSS
	 * return valid string or URL
	 */
	public static function validate_input($string, $url=false)
	{
		if($string == '' || !$string){
			return null;
		}
		
		if($url){
			$string = filter_var($string, FILTER_VALIDATE_URL);
		}else{
			$string = filter_var($string, FILTER_SANITIZE_STRING);
			// only support for name/username
			$string = preg_replace("/[^a-z 0-9-_'.]+/i", "", $string);
		}
		
		// for sanitize (">) ('>)
		if($string == 34 || $string == 39){
			return null;	
		}
		
		return $string;
	}
	
	/*
	 * for validating input - prevent SQL injection & XSS
	 * return valid string without space
	 */
	public static function validate_input_word($string)
	{
		if($string == '' || !$string){
			return null;
		}
		
		$string = filter_var($string, FILTER_SANITIZE_STRING);
		// only support for name/username
		$string = preg_replace("/[^a-z0-9_.]+/i", "", $string);
		
		// for sanitize (">) ('>)
		if($string == 34 || $string == 39){
			return null;	
		}
		
		return $string;
	}
	
	public static function validate_input_email($string)
	{
		$val = filter_var($string, FILTER_VALIDATE_EMAIL);
        if ($val) {
            return $string;
        }
        return null;
	}

	/*
     * allow all characters within "FILTER_SANITIZE_MAGIC_QUOTES"
     * remove symbol backslash (\) before single quote (') and double quote (")
     * @param type $string
     * @param type $default
     * @return type
     */
    public static function validate_input_string($string) {
        if ($string == '' || !$string) {
            return null;
        }
        $val = filter_var($string, FILTER_SANITIZE_MAGIC_QUOTES);
        return htmlspecialchars(stripslashes($val));
    }

    /*
     * allow all characters within "FILTER_SANITIZE_MAGIC_QUOTES"
     * if it contains symbols: single quote (') and double quote (") then it adds symbol backslash (\) before those symbols
     * @param type $string
     * @param type $default
     * @return type
     */
    public static function validate_input_text($string) {
        if ($string == '' || !$string) {
            return null;
        }
        return htmlspecialchars(filter_var($string, FILTER_SANITIZE_MAGIC_QUOTES));
    }

    
    /*
     * for hashing string
     */
	public static function hashing_this($string)
	{
        return substr(md5($string), 5, 25);
	}
	
	/**
     * @author Vicky Budiman vickzkater@gmail.com
     * 2019-10-26
     * 
	 * generate parent-child data from array object
     * 
     * @param array_object $array_object - required | array objects
     * @param string $parent - required | for set parent index
     * @param array $params_child - optional | for set what data that you want to save in children array | sample: ['id', 'name', 'description']
     * 
     * @return array
	 * 
	 * *Sample:
	 *  > before
	 *  array:3 [
	 * 		0 => { "category": "Hardware", "name": "CPU" }
	 * 		1 => { "category": "Hardware", "name": "Monitor" }
	 * 		2 => { "category": "Software", "name": "OS" }
	 * 	]
	 * 
	 * 	> after
	 * 	array:2 [
	 * 		"Hardware" => array:2 [
	 * 			0 => { "name": "CPU" }
	 * 			1 => { "name": "Monitor" }
	 * 		] 
	 * 		"Software" => array:1 [
	 * 			0 => { "name": "OS" }
	 * 		] 
	 * 	]
	 */
	public static function generate_parent_child_data($array_object, $parent, $params_child = null)
	{
        if($array_object && count($array_object) > 0) 
        {
            $parents = [];
            $child = [];
            $tmp = [];
            
            foreach ($array_object as $value) 
            {
                if(!in_array($value->$parent, $parents))
                {
                    $parents[] = $value->$parent;

                    if(count($tmp) > 0)
                    {
                        $child[] = $tmp;
                    }
                    $tmp = [];
                }

				$obj = new \stdClass();
				
                if($params_child)
                {
                    // set children data by params
                    foreach ($params_child as $param) 
                    {
                        if(isset($value->$param))
                        {
							$obj->$param = $value->$param;
                        }
                        else
                        {
							$obj->$param = '';
						}
					}
                }
                else
                {
                    // default for set children data
					$obj->id = $value->id;
				}

                $tmp[] = $obj;
            }

            // save the last child data
            $child[] = $tmp;

            $array = [];
            for ($i=0; $i < count($child); $i++) 
            { 
                $array[$parents[$i]] = $child[$i];
			}
			
			return $array;
		}
		else{
            // return empty array
			return array();
		}
	}
	
	public static function get_total_incoming()
    {
        $data = Product::where([
                'products.isDeleted' => 0,
                'products.price_now' => 0
            ])
            ->count();

        return $data;
    }

    public static function authorizing($module_name, $rule_name)
    {
        // special access for "Super Administrator" group
        $admin = Session::get('admin');
        if ($admin->group_id == 1)
        {
            return ['status'=>'true'];
        }

        if (empty($module_name) || empty($rule_name))
        {
            return ['status'=>'false', 'message'=>'Sorry, you are unauthorized'];
        }

        // get access from session
        $access = Session::get('access');

        $granted = false;
        foreach ($access as $item) 
        {
            if ($item->module_name == $module_name && $item->rule_name == $rule_name)
            {
                $granted = true;
                break;
            }
        }

        if ($granted)
        {
            return ['status'=>'true'];
        }

        // UNAUTHORIZED...
        return ['status'=>'false', 'message'=>'Sorry, you are  unauthorized for access '.$rule_name.' in '.$module_name];
    }

    public static function authorizing_division($division_name)
    {
        // special access for "Super Administrator" group
        $admin = Session::get('admin');
        if ($admin->group_id == 1)
        {
            return ['status'=>'true', 'message'=>'all'];
        }

        if (empty($division_name))
        {
            return ['status'=>'false', 'message'=>'Sorry, you are unauthorized'];
        }

        return ['status'=>'true', 'message'=>$division_name];
    }
}