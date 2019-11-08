<?php

use Illuminate\Support\Facades\Session;

if ( ! function_exists('lang'))
{
    /**
     * @author Vicky Budiman vickzkater@gmail.com
     * 
     * this function need data $translation from `app/Http/Controllers/Controller.php`
     * for set the translation phrase
     * 
     * @param string $phrase - phrase for get the translation based language used | required
     * @param array $translation - translation data | optional
     * @param array $replace_words - replace words in phrase, sample: ['#item' => 'brand', '#name' => 'Vicky'] | optional
     */
    function lang ($phrase, $translation = [], $replace_words = [])
    {
        // if not found the translation, search from database
        if (!is_array($translation) || count($translation) < 1)
        {
            // get default language
            $default_lang = env('DEFAULT_LANGUAGE', 'EN');

            // set language
            $language = Session::get('language');
            if (empty($language))
            {
                $language = $default_lang;
            }

            // get language data
            $translation = DB::table('language_master_detail')->select('language_master.phrase', 'language_master_detail.translate')
                ->leftJoin('language', 'language.id', 'language_master_detail.language_id')
                ->leftJoin('language_master', 'language_master.id', 'language_master_detail.language_master_id')
                ->where('language.alias', $language)
                ->where('language_master.phrase', $phrase)
                ->first();

            if (!empty($translation))
            {
                $result = $translation->translate;
            }
            else
            {
                $result = $phrase;
            }
        }
        else if (isset($translation[$phrase]))
        {
            // if translation is set & data is found
            $result = $translation[$phrase];
        }
        else
        {
            // if not found the translation, just return the param
            $result = $phrase;
        }
        
        // replace words
        if (is_array($replace_words) && count($replace_words) > 0)
        {
            foreach ($replace_words as $key => $value) 
            {
                $result = str_ireplace($key, $value, $result);
            }
        }

        return $result;
    }
}

if ( ! function_exists('set_input_form'))
{
    /**
     * @author Vicky Budiman vickzkater@gmail.com
     * 
     * for generate input element form
     * 
     * samples:
     * set_input_form ('number_format', 'nominal', 'Nominal DP', $data, $errors, true, 'only numeric', null, null, 'autocomplete="off"', null, ['Rp']);
     * set_input_form ('select2', 'brand_id', 'Brand', $data, $errors, true, 'Please Choose One', null, null, 'autocomplete="off"', $brands, ['id', 'name']);
     * set_input_form('textarea', 'summary', Summary, $data, $errors, true, null, null, null, null, null, [5]);
     * set_input_form('switch', 'status', 'Status', $data, $errors);
     * set_input_form('switch', 'posted', 'Posted', $data, $errors, false, null, null, null, null, null, ['unchecked']);
     */
    function set_input_form ($type, $input_name, $label_name, $data, $errors, $required = false, $placeholder = null, $id_name = null, $value = null, $attributes = null, $defined_data = null, $options = [])
    {
        // set error class
        $bad_item = '';
        if ($errors->has($input_name))
        {
            $bad_item = 'bad item';
        }

        // set required in label
        $span_required = '';
        $required_status = '';
        if ($required)
        {
            $span_required = '<span class="required">*</span>';
            $required_status = 'required="required"';
        }

        // set value
        if(empty($value))
        {
            if(old($input_name))
            { 
                $value = old($input_name); 
            }
            elseif (isset($data->$input_name))
            { 
                $value = $data->$input_name; 
            }
        }

        // set id element
        if (!$id_name)
        {
            $id_name = $input_name;
        }

        // pre-define element form input
        $element = '<div class="form-group '.$bad_item.' vinput_'.$input_name.'">';
        $element .= '<label class="control-label col-md-3 col-sm-3 col-xs-12" for="'.$input_name.'">'.$label_name.' '.$span_required.'</label>';
        $element .= '<div class="col-md-6 col-sm-6 col-xs-12">';

        // set default properties of element input form
        $properties = 'name="'.$input_name.'" id="'.$id_name.'" placeholder="'.$placeholder.'" '.$required_status.' '.$attributes;

        // set element input form
        switch ($type) 
        {
            case 'hidden':
                $input_element = '<input type="hidden" value="'.$value.'" '.$properties.' />';
                break;

            case 'capital':
                $input_element = '<input type="text" value="'.$value.'" '.$properties.' class="form-control col-md-7 col-xs-12" style="text-transform: uppercase !important;" />';
                break;

            case 'number':
                $input_element = '<input type="number" value="'.$value.'" '.$properties.' class="form-control col-md-7 col-xs-12" />';
                break;

            case 'number_only':
                $input_element = '<input type="text" value="'.$value.'" '.$properties.' class="form-control col-md-7 col-xs-12" onkeyup="numbers_only(this);" />';
                break;

            case 'textarea':
                $attr = '';
                if (is_array($options) && count($options) > 0)
                {
                    if (is_numeric($options[0]))
                    {
                        $attr = 'rows="'.(int) $options[0].'"';
                    }
                }
                $input_element = '<textarea '.$properties.' '.$attr.' class="form-control col-md-7 col-xs-12">'.$value.'</textarea>';
                break;

            case 'switch':
                $checked = 'checked'; // default is checked
                if(count($options) > 0 && $options[0] != 'checked' && empty($value) || (isset($options[1]) && $options[1]=='always'))
                {
                    $checked = '';
                }
                
                // for status or true/false (1/0)
                $values = ["0", "1"];
                if (empty($value) || in_array($value, $values))
                {
                    if (isset($data->$input_name) && $data->$input_name === $values[0])
                    { 
                        $checked = '';
                    }
                    elseif(old($input_name) === $values[0])
                    { 
                        $checked = ''; 
                    }
                    $input_element = '<div><label><input type="checkbox" class="js-switch" name="'.$input_name.'" id="'.$id_name.'" value="'.$values[1].'" '.$checked.' '.$attributes.' /></label></div>';
                }
                else
                {
                    $input_element = '<div><label><input type="checkbox" class="js-switch" name="'.$input_name.'" id="'.$id_name.'" value="'.$value.'" '.$checked.' '.$attributes.' /></label></div>';
                }
                break;

            case 'datepicker':
                if ($value)
                {
                    // convert date format
                    $date_arr = explode('-', $value);
                    if (count($date_arr) > 0)
                    {
                        $date_formatted = $date_arr[2].'/'.$date_arr[1].'/'.$date_arr[0];
                        $value = $date_formatted; 
                    }
                }
                $input_element = '<div class="input-group date input-datepicker" id="'.$id_name.'">';
                $input_element .= '<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>';
                $input_element .= '<input type="text" value="'.$value.'" '.$properties.' class="form-control" autocomplete="off" /></div>';
                break;

            case 'select2':
                $input_element = '<select '.$properties.' class="form-control select2"><option value="" disabled selected>- '.$placeholder.' -</option>';
                // set options
                if (!empty($defined_data))
                {
                    $set_value = 'id';
                    $set_label = 'name';
                    if (count($options) >= 2)
                    {
                        $set_value = $options[0];
                        $set_label = $options[1];
                    }
                    foreach ($defined_data as $item) 
                    {
                        $stats = '';
                        if ($item->$set_value == $value)
                        {
                            $stats = 'selected';
                        }
                        $input_element .= '<option value="'.$item->$set_value.'" '.$stats.'>'.$item->$set_label.'</option>';
                    }
                }
                else
                {
                    $input_element .= '<option value="" disabled>NO DATA</option>';
                }
                $input_element .= '</select>';
                break;

            case 'number_format':
                $input_addon = 'Rp';
                if (is_array($options))
                {
                    if(isset($options[0]))
                    {
                        $input_addon = $options[0];
                    }
                }
                $input_element = '<div class="input-group">';
                $input_element .= '<span class="input-group-addon">'.$input_addon.'</span>';
                $input_element .= '<input type="text" value="'.number_format($value).'" '.$properties.' class="form-control col-md-7 col-xs-12" onkeyup="numbers_only(this);this.value=number_format(this.value);" />';
                $input_element .= '</div>';
                break;
            
            default:
                // text
                $input_element = '<input type="text" value="'.$value.'" '.$properties.' class="form-control col-md-7 col-xs-12" />';
                break;
        }

        $element .= $input_element;
        if ($errors->has($input_name))
        {
            $element .= '<div class="text-danger">'.$errors->first($input_name).'</div>';
        }
        $element .= '</div></div>';

        // special case
        if ($type == 'hidden')
        {
            $element = $input_element;
        }

        return $element;
    }
}
