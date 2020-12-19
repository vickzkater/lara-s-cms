<?php

namespace App\Http\Controllers\Admin\system;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Validator;

// LIBRARIES
use App\Libraries\Helper;

// MODELS
use App\Models\system\SysLog;
use App\Models\system\SysBranch;
use App\Models\system\SysDivision;

class BranchController extends Controller
{
    // SET THIS MODULE
    private $module = 'Branch';
    // SET THIS OBJECT/ITEM NAME
    private $item = 'branch';

    public function list()
    {
        // AUTHORIZING...
        $authorize = Helper::authorizing($this->module, 'View List');
        if ($authorize['status'] != 'true') {
            return back()->with('error', $authorize['message']);
        }

        // FOR DISPLAY ACTIVE DATA
        $data = true;

        // GET THE DATA
        $divisions = SysDivision::orderBy('ordinal')->get();

        return view('admin.system.branch.list', compact('data', 'divisions'));
    }

    public function create()
    {
        // AUTHORIZING...
        $authorize = Helper::authorizing($this->module, 'Add New');
        if ($authorize['status'] != 'true') {
            return back()->with('error', $authorize['message']);
        }

        // AUTHORIZING DIVISION...
        $allowed_divisions = [];
        $sessions = Session::all();
        foreach ($sessions['division'] as $item) {
            $authorize_division = Helper::authorizing_division($item);
            if ($authorize_division['status'] == 'true') {
                if ($authorize_division['message'] == 'all') {
                    break;
                } else {
                    $allowed_divisions[] = $authorize_division['message'];
                }
            } else {
                return back()->with('error', $authorize['message']);
            }
        }

        $divisions = SysDivision::where('status', 1);

        // GET ONLY ALLOWED DIVISION
        if (count($allowed_divisions) > 0) {
            $divisions->where(function ($query) use ($allowed_divisions) {
                foreach ($allowed_divisions as $item) {
                    $query->orWhere('name', '=', $item);
                }
            });
        }
        $divisions = $divisions->get();

        return view('admin.system.branch.form', compact('divisions'));
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
            'division_id' => 'required|integer',
            'name' => 'required'
        ];
        $message = [
            'required' => ':attribute ' . lang('field is required', $this->translation)
        ];
        $names = [
            'division_id' => ucwords(lang('office', $this->translation)),
            'name' => ucwords(lang('name', $this->translation))
        ];
        $this->validate($request, $validation, $message, $names);

        // HELPER VALIDATION FOR PREVENT SQL INJECTION & XSS ATTACK
        $division_id = (int) $request->division_id;
        if ($division_id < 1) {
            return back()
                ->withInput()
                ->with('error', lang('#item must be chosen at least one', $this->translation, ['#item' => ucwords(lang('office', $this->translation))]));
        }
        $name = Helper::validate_input_text($request->name);
        if (!$name) {
            return back()
                ->withInput()
                ->with('error', lang('Invalid format for #item', $this->translation, ['#item' => ucwords(lang('name', $this->translation))]));
        }
        $location = Helper::validate_input_text($request->location);
        $phone = $request->phone;
        if ($phone) {
            $valid_phone = Helper::validate_phone($request->phone, '62', '0');
            if ($valid_phone['status'] != 'true') {
                return back()
                    ->withInput()
                    ->with('error', $valid_phone['message']);
            }
            $phone = $valid_phone['data'];
        }
        $gmaps = Helper::validate_input_url($request->gmaps);
        $status = (int) $request->status;

        // SET ORDER / ORDINAL
        $last = SysBranch::select('ordinal')->orderBy('ordinal', 'desc')->where('division_id', $division_id)->first();
        $ordinal = 1;
        if ($last) {
            $ordinal = $last->ordinal + 1;
        }

        // SAVE THE DATA
        $data = new SysBranch();
        $data->division_id = $division_id;
        $data->name = $name;
        $data->location = $location;
        $data->phone = $phone;
        $data->gmaps = $gmaps;
        $data->ordinal = $ordinal;
        $data->status = $status;

        if ($data->save()) {
            // LOGGING
            $log = new SysLog();
            $log->subject = Session::get('admin')->id;
            $log->action = 13;
            $log->object = $data->id;
            $log->save();

            // SUCCESS
            return redirect()
                ->route('admin.branch.list')
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

        // AUTHORIZING DIVISION...
        $allowed_divisions = [];
        $sessions = Session::all();
        foreach ($sessions['division'] as $item) {
            $authorize_division = Helper::authorizing_division($item);
            if ($authorize_division['status'] == 'true') {
                if ($authorize_division['message'] == 'all') {
                    break;
                } else {
                    $allowed_divisions[] = $authorize_division['message'];
                }
            } else {
                return back()->with('error', $authorize['message']);
            }
        }

        // SET THIS OBJECT/ITEM NAME BASED ON TRANSLATION
        $this->item = ucwords(lang($this->item, $this->translation));

        // CHECK OBJECT ID
        if ((int) $id < 1) {
            // INVALID OBJECT ID
            return redirect()
                ->route('admin.branch.list')
                ->with('error', lang('#item ID is invalid, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        // GET THE DATA BASED ON ID
        $data = SysBranch::find($id);

        // CHECK IS DATA FOUND
        if (!$data) {
            // DATA NOT FOUND
            return redirect()
                ->route('admin.branch.list')
                ->with('error', lang('#item not found, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        $divisions = SysDivision::where('status', 1);

        // GET ONLY ALLOWED DIVISION
        if (count($allowed_divisions) > 0) {
            $divisions->where(function ($query) use ($allowed_divisions) {
                foreach ($allowed_divisions as $item) {
                    $query->orWhere('name', '=', $item);
                }
            });
        }
        $divisions = $divisions->get();

        return view('admin.system.branch.form', compact('data', 'divisions'));
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
                ->route('admin.branch.list')
                ->with('error', lang('#item ID is invalid, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        // LARAVEL VALIDATION
        $validation = [
            'division_id' => 'required|integer',
            'name' => 'required'
        ];
        $message = [
            'required' => ':attribute ' . lang('field is required', $this->translation)
        ];
        $names = [
            'division_id' => ucwords(lang('division', $this->translation)),
            'name' => ucwords(lang('name', $this->translation))
        ];
        $this->validate($request, $validation, $message, $names);

        // HELPER VALIDATION FOR PREVENT SQL INJECTION & XSS ATTACK
        $division_id = (int) $request->division_id;
        if ($division_id < 1) {
            return back()
                ->withInput()
                ->with('error', lang('#item must be chosen at least one', $this->translation, ['#item' => ucwords(lang('division', $this->translation))]));
        }
        $name = Helper::validate_input_text($request->name);
        if (!$name) {
            return back()
                ->withInput()
                ->with('error', lang('Invalid format for #item', $this->translation, ['#item' => ucwords(lang('name', $this->translation))]));
        }
        $location = Helper::validate_input_text($request->location);
        $phone = $request->phone;
        if ($phone) {
            $valid_phone = Helper::validate_phone($request->phone, '62', '0');
            if ($valid_phone['status'] != 'true') {
                return back()
                    ->withInput()
                    ->with('error', $valid_phone['message']);
            }
            $phone = $valid_phone['data'];
        }
        $gmaps = Helper::validate_input_url($request->gmaps);
        $status = (int) $request->status;

        // GET THE DATA BASED ON ID
        $data = SysBranch::find($id);

        // CHECK IS DATA FOUND
        if (!$data) {
            // DATA NOT FOUND
            return back()
                ->withInput()
                ->with('error', lang('#item not found, please reload your page before resubmit', $this->translation, ['#item' => $this->item]));
        }

        // UPDATE THE DATA
        $data->division_id = $division_id;
        $data->name = $name;
        $data->location = $location;
        $data->phone = $phone;
        $data->gmaps = $gmaps;
        $data->status = $status;

        if ($data->save()) {
            // logging
            $log = new SysLog();
            $log->subject = Session::get('admin')->id;
            $log->action = 14;
            $log->object = $data->id;
            $log->save();

            // SUCCESS
            return redirect()
                ->route('admin.branch.edit', $id)
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
                ->route('admin.branch.list')
                ->with('error', lang('#item ID is invalid, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        // GET THE DATA BASED ON ID
        $data = SysBranch::find($id);

        // CHECK IS DATA FOUND
        if (!$data) {
            // DATA NOT FOUND
            return redirect()
                ->route('admin.branch.list')
                ->with('error', lang('#item not found, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        // DELETE THE DATA
        if ($data->delete()) {
            // LOGGING
            $log = new SysLog();
            $log->subject = Session::get('admin')->id;
            $log->action = 15;
            $log->object = $data->id;
            $log->save();

            // SUCCESS
            return redirect()
                ->route('admin.branch.list')
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

        // GET THE DATA
        $divisions = SysDivision::orderBy('ordinal')->get();

        return view('admin.system.branch.list', compact('divisions'));
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
                ->route('admin.branch.deleted')
                ->with('error', lang('#item ID is invalid, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        // GET THE DATA BASED ON ID
        $data = SysBranch::onlyTrashed()->find($id);

        // CHECK IS DATA FOUND
        if (!$data) {
            // DATA NOT FOUND
            return redirect()
                ->route('admin.branch.deleted')
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
                ->route('admin.branch.deleted')
                ->with('success', lang('Successfully restored #item : #name', $this->translation, ['#item' => $this->item, '#name' => $data->name]));
        }

        // FAILED
        return back()
            ->with('error', lang('Oops, failed to restore #item. Please try again.', $this->translation, ['#item' => $this->item]));
    }

    public function sorting(Request $request)
    {
        // AJAX OR API VALIDATOR
        $validation_rules = [
            'rows' => 'required'
        ];

        $validator = Validator::make($request->all(), $validation_rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'false',
                'message' => 'Validation Error',
                'data' => $validator->errors()->messages()
            ]);
        }

        // JSON Array - sample: row[]=2&row[]=1&row[]=3
        $rows = $request->input('rows');

        // convert to array
        $data = explode('&', $rows);

        $ordinal = 1;
        foreach ($data as $item) {
            // split the data
            $tmp = explode('[]=', $item);

            $object = SysBranch::find($tmp[1]);
            $object->ordinal = $ordinal;
            $object->save();

            $ordinal++;
        }

        // SUCCESS
        $response = [
            'status' => 'true',
            'message' => 'Successfully rearranged data',
            'data' => $data
        ];
        return response()->json($response, 200);
    }

    public function get_data(Request $request)
    {
        // AUTHORIZING...
        $authorize = Helper::authorizing($this->module, 'View List');
        if ($authorize['status'] != 'true') {
            return response()->json([
                'status' => 'false',
                'message' => $authorize['message']
            ]);
        }

        // SET THIS OBJECT/ITEM NAME BASED ON TRANSLATION
        $this->item = ucwords(lang($this->item, $this->translation));

        // AJAX OR API VALIDATOR
        $validation_rules = [
            'division' => 'required'
        ];
        $validator = Validator::make($request->all(), $validation_rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'false',
                'message' => 'Validation Error',
                'data' => $validator->errors()->messages()
            ]);
        }

        // GET PARAMATERS
        $division = (int) $request->input('division');

        // AUTHORIZING DIVISION...
        $allowed_divisions = [];
        $sessions = Session::all();
        foreach ($sessions['division'] as $item) {
            $authorize_division = Helper::authorizing_division($item);
            if ($authorize_division['status'] == 'true') {
                if ($authorize_division['message'] == 'all') {
                    break;
                } else {
                    $allowed_divisions[] = $authorize_division['message'];
                }
            } else {
                return back()->with('error', $authorize['message']);
            }
        }

        // GET THE DATA
        $query = SysBranch::select('sys_branches.*', 'sys_divisions.name as division_name')
            ->leftJoin('sys_divisions', 'sys_branches.division_id', '=', 'sys_divisions.id');

        // GET ONLY ALLOWED DIVISION
        if (count($allowed_divisions) > 0) {
            $query->where(function ($query_where) use ($allowed_divisions) {
                foreach ($allowed_divisions as $item) {
                    $query_where->orWhere('sys_divisions.name', '=', $item);
                }
            });
        }

        // FILTER THE DATA
        if ($division != 'all') {
            $query->where('sys_divisions.id', $division);
        }

        // PROVIDE THE DATA
        $data = $query->orderBy('sys_branches.ordinal')->get();

        // MANIPULATE THE DATA
        if (!empty($data)) {
            foreach ($data as $item) {
                $item->created_at_edited = date('Y-m-d H:i:s');
                $item->updated_at_edited = Helper::time_ago(strtotime($item->updated_at), lang('ago', $this->translation), Helper::get_periods($this->translation));
                $item->deleted_at_edited = Helper::time_ago(strtotime($item->deleted_at), lang('ago', $this->translation), Helper::get_periods($this->translation));
            }
        }

        // SUCCESS
        $response = [
            'status' => 'true',
            'message' => 'Successfully get data list',
            'data' => $data
        ];
        return response()->json($response, 200);
    }

    public function get_data_deleted(Request $request)
    {
        // AUTHORIZING...
        $authorize = Helper::authorizing($this->module, 'View List');
        if ($authorize['status'] != 'true') {
            return response()->json([
                'status' => 'false',
                'message' => $authorize['message']
            ]);
        }

        // SET THIS OBJECT/ITEM NAME BASED ON TRANSLATION
        $this->item = ucwords(lang($this->item, $this->translation));

        // AJAX OR API VALIDATOR
        $validation_rules = [
            'division' => 'required'
        ];
        $validator = Validator::make($request->all(), $validation_rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'false',
                'message' => 'Validation Error',
                'data' => $validator->errors()->messages()
            ]);
        }

        // GET PARAMATERS
        $division = (int) $request->input('division');

        // AUTHORIZING DIVISION...
        $allowed_divisions = [];
        $sessions = Session::all();
        foreach ($sessions['division'] as $item) {
            $authorize_division = Helper::authorizing_division($item);
            if ($authorize_division['status'] == 'true') {
                if ($authorize_division['message'] == 'all') {
                    break;
                } else {
                    $allowed_divisions[] = $authorize_division['message'];
                }
            } else {
                return back()->with('error', $authorize['message']);
            }
        }

        // GET THE DATA
        $query = SysBranch::onlyTrashed()
            ->select('sys_branches.*', 'sys_divisions.name as division_name')
            ->leftJoin('sys_divisions', 'sys_branches.division_id', '=', 'sys_divisions.id');

        // GET ONLY ALLOWED DIVISION
        if (count($allowed_divisions) > 0) {
            $query->where(function ($query_where) use ($allowed_divisions) {
                foreach ($allowed_divisions as $item) {
                    $query_where->orWhere('sys_divisions.name', '=', $item);
                }
            });
        }

        // FILTER THE DATA
        if ($division != 'all') {
            $query->where('sys_divisions.id', $division);
        }

        // PROVIDE THE DATA
        $data = $query->orderBy('sys_branches.ordinal')->get();

        // MANIPULATE THE DATA
        if (!empty($data)) {
            foreach ($data as $item) {
                $item->created_at_edited = date('Y-m-d H:i:s');
                $item->updated_at_edited = Helper::time_ago(strtotime($item->updated_at), lang('ago', $this->translation), Helper::get_periods($this->translation));
                $item->deleted_at_edited = Helper::time_ago(strtotime($item->deleted_at), lang('ago', $this->translation), Helper::get_periods($this->translation));
            }
        }

        // SUCCESS
        $response = [
            'status' => 'true',
            'message' => 'Successfully get data list',
            'data' => $data
        ];
        return response()->json($response, 200);
    }
}
