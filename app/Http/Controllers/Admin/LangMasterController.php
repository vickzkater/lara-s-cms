<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Language_Master;

// library
use App\Libraries\Helper;

class LangMasterController extends Controller
{
    // set this module
    private $module = 'Language Master';

    public function list(Request $request)
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'View List');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

        $data = Language_Master::orderBy('phrase');

        if(isset($request->search) && !empty($request->search)){
            $search = $request->search;
            $data->where(function ($query) use ($search) {
                $query->where('phrase', 'LIKE', '%'.$search.'%');
            });
        }

        $data = $data->paginate(10);

        return view ('admin.language_master.list', compact('data'));
    }

    public function create()
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'Add New');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

       return view('admin.language_master.form');
    }

    public function do_create(Request $request)
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'Add New');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

        $validation = [
            'phrase' => 'required|unique:language_master,phrase'
        ];

        $message    = [
            'required' => ':attribute field is required'
        ];

        $names      = [
            'phrase' => 'Phrase'
        ];

        $this->validate($request, $validation, $message, $names);

        // validating prevent SQL injection
        $phrase = Helper::validate_input_string($request->phrase);
        if(!$phrase){
            return back()->withInput()->with('error', 'Invalid format for Phrase');
        }
        $status = (int) $request->status;

        $data = new Language_Master();
        $data->phrase = $phrase;
        $data->status = $status;

        if($data->save()){
            if(Helper::validate_input_string($request->input_again) == 'yes'){
                return redirect()->route('admin_langmaster_create')->with('success', 'Successfully added a new language master : '.$phrase);
            }
            return redirect()->route('admin_langmaster_list')->with('success', 'Successfully added a new language master : '.$phrase);
        }

        return back()->withInput()->with('error', 'Oops, failed to add a new language master. Please try again.');
    }

    public function edit($id)
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'View Details');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

        if((int) $id < 1){
            return redirect()->route('admin_langmaster_list')->with('error', 'Language Master ID is invalid, please recheck your link again');
        }

        $data = Language_Master::find($id);

        if(!$data){
            return redirect()->route('admin_langmaster_list')->with('error', 'Language Master not found, please recheck your link again');
        }

        return view('admin.language_master.form', compact('data'));
    }

    public function do_edit($id, Request $request)
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'Edit');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

        $validation = [
            'phrase' => 'required'
        ];

        $message    = [
            'required' => ':attribute field is required'
        ];

        $names      = [
            'phrase' => 'Phrase'
        ];

        $this->validate($request, $validation, $message, $names);

        // validating prevent SQL injection
        $phrase = Helper::validate_input_string($request->phrase);
        if(!$phrase){
            return back()->withInput()->with('error', 'Invalid format for Phrase');
        }
        $status = (int) $request->status;

        // get existing data
        $data = Language_Master::find($id);

        if(!$data){
            return back()->withInput()->with('error', 'Language Master not found, please reload your page before resubmit');
        }

        $data->phrase = $phrase;
        $data->status = $status;

        if($data->save()){
            return redirect()->route('admin_langmaster_edit', $id)->with('success', 'Successfully updated language master : '.$phrase);
        }
        return back()->withInput()->with('error', 'Oops, failed to updated language master. Please try again.');
    }
}
