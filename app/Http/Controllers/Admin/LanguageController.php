<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Language;
use App\Models\Language_Master;
use App\Models\Language_Master_Detail;

// library
use App\Libraries\Helper;

class LanguageController extends Controller
{
    // set this module
    private $module = 'User Manager';

    public function list()
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'View List');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

        $data = Language::orderBy('name')
            ->paginate(10);

        return view ('admin.language.list', compact('data'));
    }

    public function create()
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'Add New');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

        $master = Language_Master::orderBy('phrase')->where('status', 1)->get();
        return view('admin.language.form', compact('master'));
    }

    public function do_create(Request $request)
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'Add New');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

        $validation = [
            'name' => 'required',
            'alias' => 'required'
        ];

        $message    = [
            'required' => ':attribute field is required'
        ];

        $names      = [
            'name' => 'Name',
            'alias' => 'Alias'
        ];

        $this->validate($request, $validation, $message, $names);

        // validating prevent SQL injection
        $name = Helper::validate_input_string($request->name);
        if(!$name){
            return back()->withInput()->with('error', 'Invalid format for Name');
        }
        $alias = Helper::validate_input_string($request->alias);
        if(!$alias){
            return back()->withInput()->with('error', 'Invalid format for Alias');
        }
        $status = (int) $request->status;

        $data = new Language();
        $data->name = $name;
        $data->alias = $alias;
        $data->status = $status;

        if($data->save()){
            return redirect()->route('admin_language_list')->with('success', 'Successfully added a new language : '.$name);
        }

        return back()->withInput()->with('error', 'Oops, failed to add a new language. Please try again.');
    }

    public function edit($id)
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'View Details');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

        if((int) $id < 1){
            return redirect()->route('admin_language_list')->with('error', 'Language ID is invalid, please recheck your link again');
        }

        $data = Language::find($id);

        if(!$data){
            return redirect()->route('admin_language_list')->with('error', 'Language not found, please recheck your link again');
        }

        // get language translation
        $master_data = Language_Master::select('language_master.*', 'language_master_detail.translate')
            ->leftJoin('language_master_detail', 'language_master_detail.language_master_id', 'language_master.id')
            ->where('language_master.status', 1)
            ->where('language_master_detail.language_id', $id)
            ->orderBy('language_master.phrase')
            ->get();

        // get language master
        $master = Language_Master::where('status', 1)
            ->orderBy('phrase')
            ->get();

        return view('admin.language.form', compact('data', 'master', 'master_data'));
    }

    public function do_edit($id, Request $request)
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'Edit');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

        $validation = [
            'name' => 'required',
            'alias' => 'required'
        ];

        $message    = [
            'required' => ':attribute field is required'
        ];

        $names      = [
            'name' => 'Name',
            'alias' => 'Alias'
        ];

        $this->validate($request, $validation, $message, $names);

        // validating prevent SQL injection
        $name = Helper::validate_input_string($request->name);
        if(!$name){
            return back()->withInput()->with('error', 'Invalid format for Name');
        }
        $alias = Helper::validate_input_string($request->alias);
        if(!$alias){
            return back()->withInput()->with('error', 'Invalid format for Alias');
        }
        $status = (int) $request->status;
        $translate = [];
        if($request->translate){
            $translate = $request->translate;
        }

        // get existing data
        $data = Language::find($id);

        if(!$data){
            return back()->withInput()->with('error', 'Language not found, please reload your page before resubmit');
        }

        $data->name = $name;
        $data->alias = $alias;
        $data->status = $status;

        if($data->save()){
            if(count($translate) > 0){
                // delele old data first
                Language_Master_Detail::where('language_id', $id)->delete();

                // insert new data
                foreach ($translate as $key => $value) {
                    if($value){
                        $translation = new Language_Master_Detail();
                        $translation->language_id = $id;
                        $translation->language_master_id = $key;
                        $translation->translate = $value;
                        $translation->save();
                    }
                }
            }
            return redirect()->route('admin_language_edit', $id)->with('success', 'Successfully updated language : '.$name);
        }
        return back()->withInput()->with('error', 'Oops, failed to updated language. Please try again.');
    }
}
