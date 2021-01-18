<?php

namespace App\Http\Controllers\Admin\system;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\Datatables\Datatables;

// LIBRARIES
use App\Libraries\Helper;

// MODELS
use App\Models\system\SysRule;
use App\Models\system\SysModule;
use App\Models\system\SysLog;

class RuleController extends Controller
{
    // SET THIS MODULE
    private $module = 'Rule';
    // SET THIS OBJECT/ITEM NAME
    private $item = 'rule';

    public function list()
    {
        // AUTHORIZING...
        $authorize = Helper::authorizing($this->module, 'View List');
        if ($authorize['status'] != 'true') {
            return back()->with('error', $authorize['message']);
        }

        // FOR DISPLAY ACTIVE DATA
        $data = true;

        return view('admin.system.rule.list', compact('data'));
    }

    public function get_data(Datatables $datatables, Request $request)
    {
        // AUTHORIZING...
        $authorize = Helper::authorizing($this->module, 'View List');
        if ($authorize['status'] != 'true') {
            return back()->with('error', $authorize['message']);
        }

        // SET THIS OBJECT/ITEM NAME BASED ON TRANSLATION
        $this->item = ucwords(lang($this->item, $this->translation));

        // GET THE DATA
        $query = SysRule::select('sys_rules.id', 'sys_rules.name', 'sys_modules.name as module', 'sys_rules.status', 'sys_rules.updated_at', 'sys_rules.created_at')
            ->leftJoin('sys_modules', 'sys_rules.module_id', '=', 'sys_modules.id');

        return $datatables->eloquent($query)
            ->addColumn('item_status', function ($data) {
                if ($data->status != 1) {
                    return '<span class="label label-danger"><i>' . ucwords(lang('disabled', $this->translation)) . '</i></span>';
                }
                return '<span class="label label-success">' . ucwords(lang('enabled', $this->translation)) . '</span>';
            })
            ->addColumn('action', function ($data) {
                $html = '<a href="' . route('admin.rule.edit', $data->id) . '" class="btn btn-xs btn-primary" title="' . ucwords(lang('edit', $this->translation)) . '"><i class="fa fa-pencil"></i>&nbsp; ' . ucwords(lang('edit', $this->translation)) . '</a>';

                $html .= '<form action="' . route('admin.rule.delete') . '" method="POST" onsubmit="return confirm(\'' . lang('Are you sure to delete this #item?', $this->translation, ['#item' => $this->item]) . '\');" style="display: inline"> ' . csrf_field() . ' <input type="hidden" name="id" value="' . $data->id . '">
                <button type="submit" class="btn btn-xs btn-danger" title="' . ucwords(lang('delete', $this->translation)) . '"><i class="fa fa-trash"></i>&nbsp; ' . ucwords(lang('delete', $this->translation)) . '</button></form>';

                return $html;
            })
            ->editColumn('updated_at', function ($data) {
                return Helper::time_ago(strtotime($data->updated_at), lang('ago', $this->translation), Helper::get_periods($this->translation));
            })
            ->editColumn('created_at', function ($data) {
                return $data->created_at;
            })
            ->rawColumns(['item_status', 'action'])
            ->toJson();
    }

    public function create()
    {
        // AUTHORIZING...
        $authorize = Helper::authorizing($this->module, 'Add New');
        if ($authorize['status'] != 'true') {
            return back()->with('error', $authorize['message']);
        }

        $modules = SysModule::where('status', 1)->orderBy('name')->get();

        return view('admin.system.rule.form', compact('modules'));
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
            'module_id' => 'required|integer'
        ];
        $message = [
            'required' => ':attribute ' . lang('field is required', $this->translation)
        ];
        $names = [
            'module_id' => ucwords(lang('module', $this->translation))
        ];
        $this->validate($request, $validation, $message, $names);

        // HELPER VALIDATION FOR PREVENT SQL INJECTION & XSS ATTACK
        $module_id = (int) $request->module_id;
        if ($module_id < 1) {
            return back()
                ->withInput()
                ->with('error', lang('#item must be chosen at least one', $this->translation, ['#item' => ucwords(lang('module', $this->translation))]));
        }
        $name = Helper::validate_input_text($request->name);
        $description = Helper::validate_input_text($request->description);
        $status = (int) $request->status;

        if (!isset($request->packet)) {
            $statement_success = lang('Successfully added a new #item : #name', $this->translation, ['#item' => $this->item, '#name' => $name]);
            $statement_failed = lang('Oops, failed to add a new #item. Please try again.', $this->translation, ['#item' => $this->item]);

            // SAVE THE DATA
            $data = new SysRule();
            $data->module_id = $module_id;
            $data->name = $name;
            $data->description = $description;
            $data->status = $status;

            $result = $data->save();

            if ($result) {
                // LOGGING
                $log = new SysLog();
                $log->subject = Session::get('admin')->id;
                $log->action = 17;
                $log->object = $data->id;
                $log->save();
            }
        } else {
            $statement_success = lang('Successfully added a new #item package', $this->translation, ['#item' => $this->item]);
            $statement_failed = lang('Oops, failed to add a new #item package. Please try again.', $this->translation, ['#item' => $this->item]);

            $packets = explode('|', $request->packet);
            foreach ($packets as $item) {
                // SAVE THE DATA
                $data = new SysRule();
                $data->module_id = $module_id;
                $data->name = $item;
                $data->status = $status;

                if ($data->save()) {
                    $result = true;

                    // LOGGING
                    $log = new SysLog();
                    $log->subject = Session::get('admin')->id;
                    $log->action = 17;
                    $log->object = $data->id;
                    $log->save();
                } else {
                    $result = false;
                    break;
                }
            }
        }

        if ($result) {
            $redirect_url = 'admin.rule.list';
            if (Helper::validate_input_word($request->input_again) == 'yes') {
                $redirect_url = 'admin.rule.create';
            }

            // SUCCESS
            return redirect()
                ->route($redirect_url)
                ->with('success', $statement_success);
        }

        // FAILED
        return back()
            ->withInput()
            ->with('error', $statement_failed);
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
                ->route('admin.rule.list')
                ->with('error', lang('#item ID is invalid, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        // GET THE DATA BASED ON ID
        $data = SysRule::find($id);

        // CHECK IS DATA FOUND
        if (!$data) {
            // DATA NOT FOUND
            return redirect()
                ->route('admin.rule.list')
                ->with('error', lang('#item not found, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        $modules = SysModule::where('status', 1)->orderBy('name')->get();

        return view('admin.system.rule.form', compact('data', 'modules'));
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

        // CHECK OBJECT ID
        if ((int) $id < 1) {
            // INVALID OBJECT ID
            return redirect()
                ->route('admin.rule.list')
                ->with('error', lang('#item ID is invalid, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        // LARAVEL VALIDATION
        $validation = [
            'module_id' => 'required|integer'
        ];
        $message = [
            'required' => ':attribute ' . lang('field is required', $this->translation)
        ];
        $names = [
            'module_id' => ucwords(lang('module', $this->translation))
        ];
        $this->validate($request, $validation, $message, $names);

        // HELPER VALIDATION FOR PREVENT SQL INJECTION & XSS ATTACK
        $module_id = (int) $request->module_id;
        if ($module_id < 1) {
            return back()
                ->withInput()
                ->with('error', lang('#item must be chosen at least one', $this->translation, ['#item' => ucwords(lang('module', $this->translation))]));
        }
        $name = Helper::validate_input_text($request->name);
        $description = Helper::validate_input_text($request->description);
        $status = (int) $request->status;

        // GET THE DATA BASED ON ID
        $data = SysRule::find($id);

        // CHECK IS DATA FOUND
        if (!$data) {
            // DATA NOT FOUND
            return back()
                ->withInput()
                ->with('error', lang('#item not found, please reload your page before resubmit', $this->translation, ['#item' => $this->item]));
        }

        // UPDATE THE DATA
        $data->module_id = $module_id;
        $data->name = $name;
        $data->description = $description;
        $data->status = $status;

        if ($data->save()) {
            // LOGGING
            $log = new SysLog();
            $log->subject = Session::get('admin')->id;
            $log->action = 18;
            $log->object = $data->id;
            $log->save();

            // SUCCESS
            return redirect()
                ->route('admin.rule.edit', $id)
                ->with('success', lang('Successfully updated #item : #name', $this->translation, ['#item' => $this->item, '#name' => $name]));
        }

        // FAILED
        return back()
            ->withInput()
            ->with('error', lang('Oops, failed to update #item. Please try again.', $this->translation, ['#item' => $this->item]));
    }

    public function delete(Request $request)
    {
        // AUTHORIZING...
        $authorize = Helper::authorizing($this->module, 'Delete');
        if ($authorize['status'] != 'true') {
            return back()->with('error', $authorize['message']);
        }

        // SET THIS OBJECT/ITEM NAME BASED ON TRANSLATION
        $this->item = ucwords(lang($this->item, $this->translation));

        $id = $request->id;

        // CHECK OBJECT ID
        if ((int) $id < 1) {
            // INVALID OBJECT ID
            return redirect()
                ->route('admin.rule.list')
                ->with('error', lang('#item ID is invalid, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        // GET THE DATA BASED ON ID
        $data = SysRule::find($id);

        // CHECK IS DATA FOUND
        if (!$data) {
            // DATA NOT FOUND
            return redirect()
                ->route('admin.rule.list')
                ->with('error', lang('#item not found, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        // DELETE THE DATA
        if ($data->delete()) {
            // LOGGING
            $log = new SysLog();
            $log->subject = Session::get('admin')->id;
            $log->action = 19;
            $log->object = $data->id;
            $log->save();

            // SUCCESS
            return redirect()
                ->route('admin.rule.list')
                ->with('success', lang('Successfully deleted #item : #name', $this->translation, ['#item' => $this->item, '#name' => $data->name]));
        }

        // FAILED
        return back()
            ->with('error', lang('Oops, failed to delete #item. Please try again.', $this->translation, ['#item' => $this->item]));
    }

    public function list_deleted()
    {
        // AUTHORIZING...
        $authorize = Helper::authorizing($this->module, 'Restore');
        if ($authorize['status'] != 'true') {
            return back()->with('error', $authorize['message']);
        }

        return view('admin.system.rule.list');
    }

    public function get_data_deleted(Datatables $datatables, Request $request)
    {
        // AUTHORIZING...
        $authorize = Helper::authorizing($this->module, 'View List');
        if ($authorize['status'] != 'true') {
            return back()->with('error', $authorize['message']);
        }

        // SET THIS OBJECT/ITEM NAME BASED ON TRANSLATION
        $this->item = ucwords(lang($this->item, $this->translation));

        // GET THE DATA
        $query = SysRule::onlyTrashed()
            ->select('sys_rules.id', 'sys_rules.name', 'sys_modules.name as module', 'sys_rules.status', 'sys_rules.deleted_at', 'sys_rules.created_at')
            ->leftJoin('sys_modules', 'sys_rules.module_id', 'sys_modules.id');

        return $datatables->eloquent($query)
            ->addColumn('item_status', function ($data) {
                if ($data->status != 1) {
                    return '<span class="label label-danger"><i>' . ucwords(lang('disabled', $this->translation)) . '</i></span>';
                }
                return '<span class="label label-success">' . ucwords(lang('enabled', $this->translation)) . '</span>';
            })
            ->addColumn('action', function ($data) {
                return '<form action="' . route('admin.rule.restore') . '" method="POST" onsubmit="return confirm(\'' . lang('Are you sure to restore this #item?', $this->translation, ['#item' => $this->item]) . '\');" style="display: inline"> ' . csrf_field() . ' <input type="hidden" name="id" value="' . $data->id . '">
                <button type="submit" class="btn btn-xs btn-primary" title="' . ucwords(lang('restore', $this->translation)) . '"><i class="fa fa-check"></i>&nbsp; ' . ucwords(lang('restore', $this->translation)) . '</button></form>';
            })
            ->editColumn('deleted_at', function ($data) {
                return Helper::time_ago(strtotime($data->deleted_at), lang('ago', $this->translation), Helper::get_periods($this->translation));
            })
            ->editColumn('created_at', function ($data) {
                return $data->created_at;
            })
            ->rawColumns(['item_status', 'action'])
            ->toJson();
    }

    public function restore(Request $request)
    {
        // AUTHORIZING...
        $authorize = Helper::authorizing($this->module, 'Restore');
        if ($authorize['status'] != 'true') {
            return back()->with('error', $authorize['message']);
        }

        // SET THIS OBJECT/ITEM NAME BASED ON TRANSLATION
        $this->item = ucwords(lang($this->item, $this->translation));

        $id = $request->id;

        // CHECK OBJECT ID
        if ((int) $id < 1) {
            // INVALID OBJECT ID
            return redirect()
                ->route('admin.rule.deleted')
                ->with('error', lang('#item ID is invalid, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        // GET THE DATA BASED ON ID
        $data = SysRule::onlyTrashed()->find($id);

        // CHECK IS DATA FOUND
        if (!$data) {
            // DATA NOT FOUND
            return redirect()
                ->route('admin.rule.deleted')
                ->with('error', lang('#item not found, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        // RESTORE THE DATA
        if ($data->restore()) {
            // LOGGING
            $log = new SysLog();
            $log->subject = Session::get('admin')->id;
            $log->action = 16;
            $log->object = $data->id;
            $log->save();

            // SUCCESS
            return redirect()
                ->route('admin.rule.deleted')
                ->with('success', lang('Successfully restored #item : #name', $this->translation, ['#item' => $this->item, '#name' => $data->name]));
        }

        // FAILED
        return back()
            ->with('error', lang('Oops, failed to restore #item. Please try again.', $this->translation, ['#item' => $this->item]));
    }
}
