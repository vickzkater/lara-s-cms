<?php

namespace App\Http\Controllers\Admin\system;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// LIBRARIES
use App\Libraries\Helper;

// MODELS
use App\Models\system\SysLanguage;
use App\Models\system\SysLanguageMaster;
use App\Models\system\SysLanguageMasterDetail;

class LanguageController extends Controller
{
    // SET THIS MODULE
    private $module = 'Language';
    // SET THIS OBJECT/ITEM NAME
    private $item = 'language';

    public function list(Request $request)
    {
        // AUTHORIZING...
        $authorize = Helper::authorizing($this->module, 'View List');
        if ($authorize['status'] != 'true') {
            return back()->with('error', $authorize['message']);
        }

        // GET THE DATA
        $data = SysLanguage::orderBy('name')
            ->paginate(10);

        return view('admin.system.language.list', compact('data'));
    }

    public function create()
    {
        // AUTHORIZING...
        $authorize = Helper::authorizing($this->module, 'Add New');
        if ($authorize['status'] != 'true') {
            return back()->with('error', $authorize['message']);
        }

        // GET THE DATA
        $master = SysLanguageMaster::orderBy('phrase')
            ->where('status', 1)
            ->get();

        return view('admin.system.language.form', compact('master'));
    }

    public function do_create(Request $request)
    {
        // AUTHORIZING...
        $authorize = Helper::authorizing($this->module, 'Add New');
        if ($authorize['status'] != 'true') {
            return back()->with('error', $authorize['message']);
        }

        // SET THIS OBJECT/ITEM NAME BASED ON TRANSLATION
        $this->item = ucwords(lang($this->item, $this->translation));

        // LARAVEL VALIDATION
        $validation = [
            'name' => 'required|unique:sys_languages,name',
            'alias' => 'required|unique:sys_languages,alias'
        ];
        $message = [
            'required' => ':attribute ' . lang('field is required', $this->translation),
            'unique' => ':attribute ' . lang('has already been taken, please input another data', $this->translation)
        ];
        $names = [
            'name' => ucwords(lang('name', $this->translation)),
            'alias' => ucwords(lang('alias', $this->translation))
        ];
        $this->validate($request, $validation, $message, $names);

        // HELPER VALIDATION FOR PREVENT SQL INJECTION & XSS ATTACK
        $name = Helper::validate_input_text($request->name);
        if (!$name) {
            return back()
                ->withInput()
                ->with('error', lang('Invalid format for #item', $this->translation, ['#item' => ucwords(lang('name', $this->translation))]));
        }
        $alias = Helper::validate_input_text($request->alias);
        if (!$alias) {
            return back()
                ->withInput()
                ->with('error', lang('Invalid format for #item', $this->translation, ['#item' => ucwords(lang('alias', $this->translation))]));
        }
        $status = (int) $request->status;

        // SAVE THE DATA
        $data = new SysLanguage();
        $data->name = $name;
        $data->alias = $alias;
        $data->status = $status;

        if ($data->save()) {
            // LOGGING
            Helper::logging(29, $data->id);

            // SUCCESS
            return redirect()
                ->route('admin.language.list')
                ->with('success', lang('Successfully added a new #item : #name', $this->translation, ['#item' => $this->item, '#name' => $name]));
        }

        // FAILED
        return back()
            ->withInput()
            ->with('error', lang('Oops, failed to add a new #item. Please try again.', $this->translation, ['#item' => $this->item]));
    }

    public function edit($id)
    {
        // AUTHORIZING...
        $authorize = Helper::authorizing($this->module, 'View Details');
        if ($authorize['status'] != 'true') {
            return back()->with('error', $authorize['message']);
        }

        // SET THIS OBJECT/ITEM NAME BASED ON TRANSLATION
        $this->item = ucwords(lang($this->item, $this->translation));

        // CHECK OBJECT ID
        if ((int) $id < 1) {
            // INVALID OBJECT ID
            return redirect()
                ->route('admin.language.list')
                ->with('error', lang('#item ID is invalid, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        // GET THE DATA BASED ON ID
        $data = SysLanguage::find($id);

        // CHECK IS DATA FOUND
        if (!$data) {
            // DATA NOT FOUND
            return redirect()
                ->route('admin.language.list')
                ->with('error', lang('#item not found, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        // get language translation
        $master_data = SysLanguageMaster::select('sys_language_master.*', 'sys_language_master_details.translate')
            ->leftJoin('sys_language_master_details', 'sys_language_master_details.language_master_id', 'sys_language_master.id')
            ->where('sys_language_master.status', 1)
            ->where('sys_language_master_details.language_id', $id)
            ->orderBy('sys_language_master.phrase')
            ->get();

        // get language master
        $master = SysLanguageMaster::where('status', 1)
            ->orderBy('phrase')
            ->get();

        return view('admin.system.language.form', compact('data', 'master', 'master_data'));
    }

    public function do_edit($id, Request $request)
    {
        // AUTHORIZING...
        $authorize = Helper::authorizing($this->module, 'Edit');
        if ($authorize['status'] != 'true') {
            return back()->with('error', $authorize['message']);
        }

        // SET THIS OBJECT/ITEM NAME BASED ON TRANSLATION
        $this->item = ucwords(lang($this->item, $this->translation));

        // LARAVEL VALIDATION
        $validation = [
            'name' => 'required',
            'alias' => 'required'
        ];
        $message = [
            'required' => ':attribute ' . lang('field is required', $this->translation)
        ];
        $names = [
            'name' => ucwords(lang('name', $this->translation)),
            'alias' => ucwords(lang('alias', $this->translation))
        ];
        $this->validate($request, $validation, $message, $names);

        // HELPER VALIDATION FOR PREVENT SQL INJECTION & XSS ATTACK
        $name = Helper::validate_input_text($request->name);
        if (!$name) {
            return back()
                ->withInput()
                ->with('error', lang('Invalid format for #item', $this->translation, ['#item' => ucwords(lang('name', $this->translation))]));
        }
        $alias = Helper::validate_input_text($request->alias);
        if (!$alias) {
            return back()
                ->withInput()
                ->with('error', lang('Invalid format for #item', $this->translation, ['#item' => ucwords(lang('alias', $this->translation))]));
        }
        $status = (int) $request->status;
        $translate = [];
        if ($request->translate) {
            $translate = $request->translate;
        }

        // GET THE DATA BASED ON ID
        $data = SysLanguage::find($id);

        // CHECK IS DATA FOUND
        if (!$data) {
            // DATA NOT FOUND
            return back()
                ->withInput()
                ->with('error', lang('#item not found, please reload your page before resubmit', $this->translation, ['#item' => $this->item]));
        }

        // UPDATE THE DATA
        $data->name = $name;
        $data->alias = $alias;
        $data->status = $status;
        $data->updated_at = date('Y-m-d H:i:s');

        if ($data->save()) {
            if (count($translate) > 0) {
                // DELELE OLD DATA FIRST
                SysLanguageMasterDetail::where('language_id', $id)->delete();

                // INSERT NEW DATA
                foreach ($translate as $key => $value) {
                    if ($value) {
                        $translation = new SysLanguageMasterDetail();
                        $translation->language_id = $id;
                        $translation->language_master_id = $key;
                        $translation->translate = Helper::validate_input_text($value);
                        $translation->save();
                    }
                }
            }

            // SUCCESS
            return redirect()
                ->route('admin.language.edit', $id)
                ->with('success', lang('Successfully updated #item : #name', $this->translation, ['#item' => $this->item, '#name' => $name]));
        }

        // FAILED
        return back()
            ->withInput()
            ->with('error', lang('Oops, failed to update #item. Please try again.', $this->translation, ['#item' => $this->item]));
    }
}
