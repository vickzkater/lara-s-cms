<?php

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

if (!function_exists('lang')) {
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
    function lang($phrase, $translation = [], $replace_words = [])
    {
        // get default language
        $default_lang = env('DEFAULT_LANGUAGE', 'EN');

        // set language
        $language = Session::get('language');
        if (empty($language)) {
            $language = $default_lang;
        }

        // if not found the translation, search from database
        if ((!is_array($translation) || count($translation) < 1) && $language != $default_lang) {
            // get language data
            if (env('APP_BACKEND', 'MODEL') != 'API' && env('MULTILANG_MODULE', false)) {
                $translation = DB::table('sys_language_master_details')
                    ->select('sys_language_master.phrase', 'sys_language_master_details.translate')
                    ->leftJoin('sys_languages', 'sys_languages.id', 'sys_language_master_details.language_id')
                    ->leftJoin('sys_language_master', 'sys_language_master.id', 'sys_language_master_details.language_master_id')
                    ->where('sys_languages.alias', $language)
                    ->where('sys_language_master.phrase', $phrase)
                    ->first();

                if (!empty($translation)) {
                    // if found the translation, return the translation
                    $result = $translation->translate;
                } else {
                    // if not found the translation, just return the param
                    $result = $phrase;
                }
            } else {
                // coz using API as back-end mode, so we can't get translation data from database - just return the param
                $result = $phrase;
            }
        } else if (isset($translation[$phrase])) {
            // if translation is set & data is found
            $result = $translation[$phrase];
        } else {
            // if not found the translation, just return the param
            $result = $phrase;
        }

        // replace words
        if (is_array($replace_words) && count($replace_words) > 0) {
            foreach ($replace_words as $key => $value) {
                $result = str_ireplace($key, $value, $result);
            }
        }

        return htmlspecialchars_decode($result);
    }
}

if (!function_exists('set_input_form')) {
    /**
     * @author Vicky Budiman vickzkater@gmail.com
     * 
     * for generate input element form
     * 
     * samples:
     * set_input_form ('number_format', 'nominal', 'Nominal DP', $data, $errors, true, 'only numeric', null, null, 'autocomplete="off"', null, ['Rp']);
     * set_input_form ('select2', 'brand_id', 'Brand', $data, $errors, true, 'Please Choose One', null, null, 'autocomplete="off"', $brands, ['id', 'name']);
     * set_input_form ('select', 'level', 'Level', $data, $errors, true, 'Please Choose One', null, null, 'autocomplete="off"', $levels, ['id', 'name']);
     * set_input_form('textarea', 'summary', Summary, $data, $errors, true, null, null, null, null, null, [5]);
     * set_input_form('switch', 'status', 'Status', $data, $errors);
     * set_input_form('switch', 'posted', 'Posted', $data, $errors, false, null, null, null, null, null, ['unchecked']);
     */
    function set_input_form($type, $input_name, $label_name, $data, $errors, $required = false, $placeholder = null, $id_name = null, $value = null, $attributes = null, $defined_data = null, $options = [])
    {
        // set error class
        $bad_item = '';
        if ($errors->has($input_name)) {
            $bad_item = 'bad item';
        }

        // set required in label
        $span_required = '';
        $required_status = '';
        if ($required) {
            $span_required = '<span class="required">*</span>';
            $required_status = 'required="required"';
        }

        // set value
        if (empty($value)) {
            if (old($input_name)) {
                $value = old($input_name);
            } elseif (isset($data->$input_name)) {
                $value = $data->$input_name;
            }
        }

        // set id element
        if (!$id_name) {
            $id_name = $input_name;
        }

        // pre-define element form input
        $element = '<div class="form-group ' . $bad_item . ' vinput_' . $input_name . '">';
        $element .= '<label class="control-label col-md-3 col-sm-3 col-xs-12" for="' . $input_name . '">' . $label_name . ' ' . $span_required . '</label>';
        $element .= '<div class="col-md-6 col-sm-6 col-xs-12">';

        // set default properties of element input form
        $properties = 'name="' . $input_name . '" id="' . $id_name . '" placeholder="' . $placeholder . '" ' . $required_status . ' ' . $attributes;

        // set element input form
        switch ($type) {
            case 'hidden':
                $input_element = '<input type="hidden" value="' . $value . '" ' . $properties . ' />';
                break;

            case 'capital':
                $input_element = '<input type="text" value="' . $value . '" ' . $properties . ' class="form-control col-md-7 col-xs-12" style="text-transform: uppercase !important;" />';
                break;

            case 'number':
                $input_element = '<input type="number" value="' . $value . '" ' . $properties . ' class="form-control col-md-7 col-xs-12" />';
                break;

            case 'number_only':
                $input_element = '<input type="text" value="' . $value . '" ' . $properties . ' class="form-control col-md-7 col-xs-12" onkeyup="numbers_only(this);" />';
                break;

            case 'textarea':
                $attr = '';
                if (is_array($options) && count($options) > 0) {
                    if (is_numeric($options[0])) {
                        $attr = 'rows="' . (int) $options[0] . '"';
                    }
                }
                $input_element = '<textarea ' . $properties . ' ' . $attr . ' class="form-control col-md-7 col-xs-12">' . $value . '</textarea>';
                break;

            case 'switch':
                $checked = 'checked'; // default is checked
                if (count($options) > 0 && $options[0] != 'checked' && empty($value) || (isset($options[1]) && $options[1] == 'always')) {
                    $checked = '';
                }

                // for status or true/false (1/0)
                $values = ["0", "1"];
                if (empty($value) || in_array($value, $values)) {
                    if (isset($data->$input_name) && $data->$input_name == $values[0]) {
                        $checked = '';
                    } elseif (old($input_name) == $values[0]) {
                        $checked = '';
                    }
                    $input_element = '<div><label><input type="checkbox" class="js-switch" name="' . $input_name . '" id="' . $id_name . '" value="' . $values[1] . '" ' . $checked . ' ' . $attributes . ' /></label></div>';
                } else {
                    $input_element = '<div><label><input type="checkbox" class="js-switch" name="' . $input_name . '" id="' . $id_name . '" value="' . $value . '" ' . $checked . ' ' . $attributes . ' /></label></div>';
                }
                break;

            case 'datepicker':
                if ($value) {
                    if (strpos($value, '-') !== false) {
                        // convert date format
                        $date_arr = explode('-', $value);
                        if (count($date_arr) > 0) {
                            $date_formatted = $date_arr[2] . '/' . $date_arr[1] . '/' . $date_arr[0];
                            $value = $date_formatted;
                        }
                    }
                }
                $input_element = '<div class="input-group date input-datepicker" id="' . $id_name . '">';
                $input_element .= '<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>';
                $input_element .= '<input type="text" value="' . $value . '" ' . $properties . ' class="form-control" autocomplete="off" /></div>';
                break;

            case 'select2':
                $input_element = '<select ' . $properties . ' class="form-control select2"><option value="" disabled selected>- ' . $placeholder . ' -</option>';
                // set options
                if (!empty($defined_data)) {
                    $set_value = 'id';
                    $set_label = 'name';
                    if (count($options) >= 2) {
                        $set_value = $options[0];
                        $set_label = $options[1];

                        if (isset($options['label'])) {
                            if (isset($options['label_separator'])) {
                                $set_label = explode($options['label_separator'], $options['label']);
                            }
                        }
                    }

                    foreach ($defined_data as $item) {
                        $stats = '';
                        if ($item->$set_value == $value && !empty($value)) {
                            $stats = 'selected';
                        }

                        // set label
                        if (is_array($set_label)) {
                            $labels = [];
                            foreach ($set_label as $val) {
                                $labels[] = $item->$val;
                            }
                            $label = implode($options['label_separator'], $labels);
                        } else {
                            $label = $item->$set_label;
                        }

                        $input_element .= '<option value="' . $item->$set_value . '" ' . $stats . '>' . $label . '</option>';
                    }
                } else {
                    $input_element .= '<option value="" disabled>NO DATA</option>';
                }
                $input_element .= '</select>';
                break;

            case 'select':
                $input_element = '<select ' . $properties . ' class="form-control"><option value="" disabled selected>- ' . $placeholder . ' -</option>';
                // set options
                if (!empty($defined_data)) {
                    foreach ($defined_data as $key => $val) {
                        $stats = '';
                        if ($key == $value && !empty($value)) {
                            $stats = 'selected';
                        }

                        $input_element .= '<option value="' . $key . '" ' . $stats . '>' . $val . '</option>';
                    }
                } else {
                    $input_element .= '<option value="" disabled>NO DATA</option>';
                }
                $input_element .= '</select>';
                break;

            case 'number_format':
                $input_addon = 'Rp';
                if (is_array($options)) {
                    if (isset($options[0])) {
                        $input_addon = $options[0];
                    }
                }
                // sanitize the value, so it must be numeric
                $value = (int) str_replace(',', '', $value);
                $input_element = '<div class="input-group">';
                $input_element .= '<span class="input-group-addon">' . $input_addon . '</span>';
                $input_element .= '<input type="text" value="' . number_format($value) . '" ' . $properties . ' class="form-control col-md-7 col-xs-12" onkeyup="numbers_only(this);this.value=number_format(this.value);" />';
                $input_element .= '</div>';
                break;

            case 'image':
                if (empty($value) || empty($options)) {
                    // default image
                    $input_element = '<img src="' . asset('/images/no-image.png') . '" class="vimg" />';
                } else {
                    // set image using "$options" & "$value"
                    $input_element = '<img src="' . asset($options[0] . $value) . '" class="vimg" />';
                }

                $input_element .= '<input type="file" ' . $properties . ' class="form-control col-md-7 col-xs-12" accept=".jpg, .jpeg, .png" onchange="readURL(this, \'before\');" style="margin-top:5px" />';
                break;

            default:
                // text
                $input_element = '<input type="text" value="' . $value . '" ' . $properties . ' class="form-control col-md-7 col-xs-12" />';
                break;
        }

        $element .= $input_element;
        if ($errors->has($input_name)) {
            $element .= '<div class="text-danger">' . $errors->first($input_name) . '</div>';
        }
        $element .= '</div></div>';

        // special case
        if ($type == 'hidden') {
            $element = $input_element;
        }

        return $element;
    }
}

if (!function_exists('set_input_form2')) {
    /**
     * @author Vicky Budiman vickzkater@gmail.com
     * 
     * for generate input element form
     */
    function set_input_form2($type, $input_name, $label_name, $data, $errors, $required = false, $config = null)
    {
        // declare default values
        $placeholder = null;
        $id_name = null;
        $value = null;
        $attributes = null;
        $defined_data = null;
        $no_image = asset('images/no-image.png');
        $delete = false;

        // set configuration
        if ($config) {
            if (isset($config->placeholder)) {
                $placeholder = $config->placeholder;
            }
            if (isset($config->id_name)) {
                $id_name = $config->id_name;
            }
            if (isset($config->value)) {
                $value = $config->value;
            }
            if (isset($config->attributes)) {
                $attributes = $config->attributes;
            }
            // config for textarea
            if (isset($config->rows)) {
                $textarea_rows = $config->rows;
            }
            // config for textarea
            if (isset($config->autosize)) {
                $textarea_autosize = true;
            }
            // config for switch
            if (isset($config->default)) {
                $default = $config->default;
            }
            // config for select2
            if (isset($config->defined_data)) {
                $defined_data = $config->defined_data;
            }
            // config for select2
            if (isset($config->field_value)) {
                $field_value = $config->field_value;
            }
            // config for select2
            if (isset($config->field_text)) {
                $field_text = $config->field_text;
            }
            // config for select2
            if (isset($config->separator)) {
                $separator = $config->separator;
            }
            // config for number_format
            if (isset($config->input_addon)) {
                $input_addon = $config->input_addon;
            }
            // config for image
            if (isset($config->path)) {
                $path = $config->path;
            }
            // config for image
            if (isset($config->delete)) {
                $delete = $config->delete;
            }
            // config for image
            if (isset($config->info)) {
                $info = $config->info;
            }
            if (isset($config->info_text)) {
                $info_text = $config->info_text;
            }
        }

        // set error class
        $bad_item = '';
        if ($errors->has($input_name)) {
            $bad_item = 'bad item';
        }

        // set required in label
        $span_required = '';
        $required_status = '';
        if ($required) {
            $span_required = '<span class="required">*</span>';
            $required_status = 'required="required"';
        }

        // set value
        if (empty($value)) {
            if (old($input_name)) {
                $value = old($input_name);
            } elseif (isset($data->$input_name)) {
                $value = $data->$input_name;
            }
        }

        // set id element
        if (!$id_name) {
            $id_name = $input_name;
        }

        // pre-define element form input
        $element = '<div class="form-group ' . $bad_item . ' vinput_' . $input_name . '">';
        $element .= '<label class="control-label col-md-3 col-sm-3 col-xs-12" for="' . $input_name . '">' . $label_name . ' ' . $span_required . '</label>';
        $element .= '<div class="col-md-6 col-sm-6 col-xs-12">';

        // set default properties of element input form
        $properties = 'name="' . $input_name . '" id="' . $id_name . '" placeholder="' . $placeholder . '" ' . $required_status . ' ' . $attributes;

        // set element input form
        switch ($type) {
            case 'hidden':
                $input_element = '<input type="hidden" value="' . $value . '" ' . $properties . ' />';
                break;

            case 'capital':
                $input_element = '<input type="text" value="' . $value . '" ' . $properties . ' class="form-control col-md-7 col-xs-12" style="text-transform: uppercase !important;" />';
                break;

            case 'number':
                $input_element = '<input type="number" value="' . $value . '" ' . $properties . ' class="form-control col-md-7 col-xs-12" />';
                break;

            case 'number_only':
                $input_element = '<input type="text" value="' . $value . '" ' . $properties . ' class="form-control col-md-7 col-xs-12" onkeyup="numbers_only(this);" />';
                break;

            case 'textarea':
                // set rows attribute
                $attr = '';
                if (isset($textarea_rows)) {
                    $attr = 'rows="' . (int) $textarea_rows . '"';
                }
                // set autosize
                $autosize = '';
                if (isset($textarea_autosize) && $textarea_autosize == true) {
                    $autosize = 'resizable_textarea';
                }
                $input_element = '<textarea ' . $properties . ' ' . $attr . ' class="form-control col-md-7 col-xs-12 ' . $autosize . '">' . $value . '</textarea>';
                break;

            case 'switch':
                $checked = 'checked'; // default is checked
                if (empty($value)) {
                    $checked = '';
                    // set using default value: checked / ('') empty string
                    if (isset($default)) {
                        $checked = $default;
                    }
                }

                // for status or true/false (1/0)
                $values = ["0", "1"];
                if (empty($value) || in_array($value, $values)) {
                    if (isset($data->$input_name) && $data->$input_name == $values[0]) {
                        $checked = '';
                    } elseif (old($input_name) == $values[0]) {
                        $checked = '';
                    }
                    $input_element = '<div><label><input type="checkbox" class="js-switch" name="' . $input_name . '" id="' . $id_name . '" value="' . $values[1] . '" ' . $checked . ' ' . $attributes . ' /></label></div>';
                } else {
                    $input_element = '<div><label><input type="checkbox" class="js-switch" name="' . $input_name . '" id="' . $id_name . '" value="' . $value . '" ' . $checked . ' ' . $attributes . ' /></label></div>';
                }
                break;

            case 'datepicker':
                if ($value) {
                    if (strpos($value, '-') !== false) {
                        // convert date format
                        $date_arr = explode('-', $value);
                        if (count($date_arr) > 0) {
                            $date_formatted = $date_arr[2] . '/' . $date_arr[1] . '/' . $date_arr[0];
                            $value = $date_formatted;
                        }
                    }
                }
                $input_element = '<div class="input-group date input-datepicker" id="' . $id_name . '">';
                $input_element .= '<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>';
                $input_element .= '<input type="text" value="' . $value . '" ' . $properties . ' class="form-control" autocomplete="off" /></div>';
                break;

            case 'select2':
                $input_element = '<select ' . $properties . ' class="form-control select2">';
                if (!empty($placeholder)) {
                    $input_element .= '<option value="" disabled selected>' . $placeholder . '</option>';
                }
                // set options
                if (!empty($defined_data)) {
                    // default values
                    $set_value = 'id';
                    $set_label = 'name';
                    // set field for options value
                    if (isset($field_value)) {
                        $set_value = $field_value;
                    }
                    // set field for options text
                    if (isset($field_text)) {
                        $set_label = $field_text;
                        // if set options text more than 1 field
                        if (isset($separator)) {
                            $set_label = explode($separator, $field_text);
                        }
                    }
                    // set options
                    foreach ($defined_data as $item) {
                        // set "selected" attribute
                        $stats = '';
                        if ($item->$set_value == $value) {
                            $stats = 'selected';
                        }

                        // set options text
                        if (is_array($set_label)) {
                            // set options text more than 1 field
                            $labels = [];
                            foreach ($set_label as $val) {
                                $labels[] = $item->$val;
                            }
                            $label = implode($separator, $labels);
                        } else {
                            // set options text using 1 field
                            $label = $item->$set_label;
                        }
                        // set HTML
                        $input_element .= '<option value="' . $item->$set_value . '" ' . $stats . '>' . $label . '</option>';
                    }
                } else {
                    $input_element .= '<option value="" disabled>NO DATA</option>';
                }
                $input_element .= '</select>';
                break;

            case 'select':
                $input_element = '<select ' . $properties . ' class="form-control">';
                if (!empty($placeholder)) {
                    $input_element .= '<option value="" disabled selected>' . $placeholder . '</option>';
                }
                // set options
                if (!empty($defined_data)) {
                    if (isset($defined_data[0])) {
                        foreach ($defined_data as $opt) {
                            $stats = '';
                            if ($opt == $value && !empty($value)) {
                                $stats = 'selected';
                            }

                            $input_element .= '<option value="' . $opt . '" ' . $stats . '>' . $opt . '</option>';
                        }
                    } else {
                        foreach ($defined_data as $key => $val) {
                            $stats = '';
                            if ($key == $value && !empty($value)) {
                                $stats = 'selected';
                            }

                            $input_element .= '<option value="' . $key . '" ' . $stats . '>' . $val . '</option>';
                        }
                    }
                } else {
                    $input_element .= '<option value="" disabled>NO DATA</option>';
                }
                $input_element .= '</select>';
                break;

            case 'number_format':
                // sanitize the value, so it must be numeric
                $value = (int) str_replace(',', '', $value);

                $input_element = '<input type="text" value="' . number_format($value) . '" ' . $properties . ' class="form-control col-md-7 col-xs-12" onkeyup="numbers_only(this);this.value=number_format(this.value);" />';
                break;

            case 'image':
                if (empty($value)) {
                    // default image
                    $input_element = '<img src="' . $no_image . '" style="max-width:200px;" />';
                } else {
                    // set image using "$value" only
                    $img_src = asset($value);
                    if (isset($path)) {
                        // set image using "$path" & "$value"
                        $img_src = asset($path . $value);
                    }
                    $input_element = '<img src="' . $img_src . '" style="max-width:200px;" />';
                }
                $input_element .= '<input type="file" ' . $properties . ' class="form-control col-md-7 col-xs-12" accept="image/*" onchange="readURL(this, \'before\');" style="margin-top:5px" />';
                if (isset($info)) {
                    $input_element .= '<br><span>' . $info . '</span>';
                }
                if (!empty($value) && $delete) {
                    $input_element .= '<br><span class="btn btn-warning btn-xs" id="' . $id_name . '-delbtn" style="margin: 5px 0 !important;" onclick="reset_img_preview(\'#' . $id_name . '\', \'' . $no_image . '\', \'before\')">Delete uploaded image?</span>';
                    $input_element .= ' <input type="hidden" name="' . $input_name . '_delete" id="' . $input_name . '-delete">';
                }
                break;

            case 'tags':
                $input_element = '<input type="text" class="tags tagsinput form-control col-md-7 col-xs-12" value="' . $value . '" ' . $properties . ' />';
                break;

            case 'email':
                $input_element = '<input type="email" value="' . $value . '" ' . $properties . ' class="form-control col-md-7 col-xs-12" />';
                break;

            case 'password':
                $input_element = '<input type="password" value="' . $value . '" ' . $properties . ' class="form-control col-md-7 col-xs-12" />';
                break;

            case 'word':
                $input_element = '<input type="text" value="' . $value . '" ' . $properties . ' class="form-control col-md-7 col-xs-12" onkeyup="username_only(this);" />';
                break;

            case 'file':
                $input_element = '';
                if (!empty($value)) {
                    // validate $value is local path or link
                    $string = filter_var($value, FILTER_VALIDATE_URL);
                    // for sanitize (">) ('>)
                    if ($string == 34 || $string == 39 || $string == false) {
                        // local path
                        $url_value = asset($value);
                    } else {
                        $headers = get_headers($value);
                        $value_is_url = stripos($headers[0], "200 OK") ? true : false;

                        if ($value_is_url) {
                            // link
                            $url_value = $value;
                        } else {
                            // local path
                            $url_value = asset($value);
                        }
                    }

                    $input_element .= '<a href="' . $url_value . '" target="_blank" id="' . $id_name . '-file-preview">' . $url_value . ' <i class="fa fa-external-link"></i></a>';
                    if ($delete) {
                        $input_element .= '&nbsp; <span class="btn btn-danger btn-xs" id="' . $id_name . '-delbtn" style="margin: 5px 0 !important;" onclick="remove_uploaded_file(\'#' . $id_name . '\')"><i class="fa fa-trash"></i></span><br>';
                        $input_element .= ' <input type="hidden" name="' . $input_name . '_delete" id="' . $input_name . '-delete">';
                    }
                }
                $input_element .= '<input type="file" ' . $properties . ' class="form-control col-md-7 col-xs-12" />';
                break;

            default:
                // text
                $input_element = '<input type="text" value="' . $value . '" ' . $properties . ' class="form-control col-md-7 col-xs-12" />';
                break;
        }

        // set input group addon
        if (isset($input_addon)) {
            $element .= '<div class="input-group">';
            $element .= '<span class="input-group-addon">' . $input_addon . '</span>';
            $element .= $input_element;
            $element .= '</div>';
        } else {
            $element .= $input_element;
        }

        // add info text
        if (isset($info_text)) {
            $element .= '<span>' . $info_text . '</span>';
        }

        // set error message
        if ($errors->has($input_name)) {
            $element .= '<div class="text-danger">' . $errors->first($input_name) . '</div>';
        }
        $element .= '</div></div>';

        // special case
        if ($type == 'hidden') {
            $element = $input_element;
        }

        return $element;
    }
}
