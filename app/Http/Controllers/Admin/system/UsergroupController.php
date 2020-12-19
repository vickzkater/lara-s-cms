<?php

namespace App\Http\Controllers\Admin\system;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;

// LIBRARIES
use App\Libraries\Helper;

// MODELS
use App\Models\system\SysLog;
use App\Models\system\SysBranch;
use App\Models\system\SysRule;
use App\Models\system\SysGroup;
use App\Models\system\SysGroupRule;
use App\Models\system\SysGroupBranch;

class UsergroupController extends Controller
{
    // SET THIS MODULE
    private $module = 'Usergroup';
    // SET THIS OBJECT/ITEM NAME
    private $item = 'admin group';

    public function list()
    {
        // AUTHORIZING...
        $authorize = Helper::authorizing($this->module, 'View List');
        if ($authorize['status'] != 'true') {
            return back()->with('error', $authorize['message']);
        }

        // FOR DISPLAY ACTIVE DATA
        $data = true;

        return view('admin.system.usergroup.list', compact('data'));
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

        // AUTHORIZING DIVISIONS & BRANCHES...
        $allowed_divisions = [];
        $allowed_branches = [];
        $allowed = Helper::get_authorized_branches();
        if ($allowed != 'all') {
            $allowed_divisions = $allowed['allowed_divisions'];
            $allowed_branches = $allowed['allowed_branches'];
        }

        // GET THE DATA
        $query = SysGroup::select(
            'sys_groups.id',
            'sys_groups.name',
            'sys_groups.status',
            'sys_groups.created_at',
            'sys_groups.updated_at',
            DB::raw('group_concat(DISTINCT `d`.`name`) as division_name')
        )
            ->leftJoin('sys_group_branch as b', 'b.group', 'sys_groups.id')
            ->leftJoin('sys_branches as c', 'c.id', 'b.branch')
            ->leftJoin('sys_divisions as d', 'd.id', 'c.division_id')
            ->where('sys_groups.id', '>', 1)
            ->groupBy('sys_groups.id', 'sys_groups.name', 'sys_groups.status', 'sys_groups.created_at', 'sys_groups.updated_at');

        // GET ONLY ALLOWED DIVISIONS
        if (count($allowed_divisions) > 0) {
            $query->where(function ($query_add) use ($allowed_divisions) {
                foreach ($allowed_divisions as $item) {
                    $query_add->orWhere('d.id', '=', $item);
                }
            });
        }
        // GET ONLY ALLOWED BRANCHES
        if (count($allowed_branches) > 0) {
            $query->where(function ($query_add) use ($allowed_branches) {
                foreach ($allowed_branches as $item) {
                    $query_add->orWhere('c.id', '=', $item);
                }
            });
        }

        return $datatables->eloquent($query)
            ->addColumn('item_status', function ($data) {
                if ($data->status != 1) {
                    return '<span class="label label-danger"><i>' . ucwords(lang('disabled', $this->translation)) . '</i></span>';
                }
                return '<span class="label label-success">' . ucwords(lang('enabled', $this->translation)) . '</span>';
            })
            ->addColumn('action', function ($data) {
                $html = '<a href="' . route('admin.usergroup.edit', $data->id) . '" class="btn btn-xs btn-primary" title="' . ucwords(lang('edit', $this->translation)) . '"><i class="fa fa-pencil"></i>&nbsp; ' . ucwords(lang('edit', $this->translation)) . '</a>';

                $html .= '<form action="' . route('admin.usergroup.delete') . '" method="POST" onsubmit="return confirm(\'' . lang('Are you sure to delete this #item?', $this->translation, ['#item' => $this->item]) . '\');" style="display: inline"> ' . csrf_field() . ' <input type="hidden" name="id" value="' . $data->id . '">
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

        // GET DATA (RULES)
        $rules = SysRule::leftJoin('sys_modules', 'sys_rules.module_id', 'sys_modules.id')
            ->where('sys_modules.status', 1)
            ->select('sys_rules.id', 'sys_rules.name', 'sys_rules.description', 'sys_rules.module_id', 'sys_modules.name as module', 'sys_rules.status', 'sys_rules.updated_at', 'sys_rules.created_at')
            ->orderBy('sys_modules.name')
            ->orderBy('sys_rules.name')
            ->get();
        $params_child = ['id', 'name', 'module_id', 'description'];
        $rules = Helper::generate_parent_child_data($rules, 'module', $params_child);

        // GET DATA (DIVISIONS)
        $divisions = SysBranch::select('sys_branches.*', 'sys_divisions.name as division_name')
            ->leftJoin('sys_divisions', 'sys_branches.division_id', '=', 'sys_divisions.id')
            ->orderBy('sys_divisions.name')
            ->orderBy('sys_branches.name');
        // GET ONLY ALLOWED DIVISION
        if (count($allowed_divisions) > 0) {
            $divisions->where(function ($query) use ($allowed_divisions) {
                foreach ($allowed_divisions as $item) {
                    $query->orWhere('sys_divisions.name', '=', $item);
                }
            });
        }
        $divisions = $divisions->get();
        $params_child = ['id', 'name', 'division_id'];
        $divisions = Helper::generate_parent_child_data($divisions, 'division_name', $params_child);

        return view('admin.system.usergroup.form', compact('rules', 'divisions'));
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
            'name' => 'required|unique:sys_groups,name'
        ];
        $message = [
            'required' => ':attribute ' . lang('field is required', $this->translation),
            'unique' => ':attribute ' . lang('has already been taken, please input another data', $this->translation)
        ];
        $names = [
            'name' => ucwords(lang('name', $this->translation))
        ];
        $this->validate($request, $validation, $message, $names);

        // HELPER VALIDATION FOR PREVENT SQL INJECTION & XSS ATTACK
        $name = Helper::validate_input_text($request->name);
        if (!$name) {
            return back()
                ->withInput()
                ->with('error', lang('Invalid format for #item', $this->translation, ['#item' => ucwords(lang('name', $this->translation))]));
        }
        $status = (int) $request->status;

        // SAVE THE DATA
        $data = new SysGroup();
        $data->name = $name;
        $data->status = $status;

        if ($data->save()) {
            /* set usergroup's access */
            $group_id = $data->id;
            if (isset($request->access)) {
                $data = [];
                foreach ($request->access as $item) {
                    $data[] = array('group_id' => $group_id, 'rule_id' => $item);
                }
                SysGroupRule::insert($data);
            }

            /* set usergroup's branch */
            if (isset($request->branch)) {
                $data = [];
                foreach ($request->branch as $item) {
                    $data[] = array('group' => $group_id, 'branch' => $item);
                }
                SysGroupBranch::insert($data);
            }

            // LOGGING
            $log = new Syslog();
            $log->subject = Session::get('admin')->id;
            $log->action = 21;
            $log->object = $group_id;
            $log->save();

            // SUCCESS
            return redirect()
                ->route('admin.usergroup.list')
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
        if ((int) $id < 2) {
            // INVALID OBJECT ID
            return redirect()
                ->route('admin.usergroup.list')
                ->with('error', lang('#item ID is invalid, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        // GET THE DATA BASED ON ID
        $data = SysGroup::find($id);

        // CHECK IS DATA FOUND
        if (!$data) {
            // DATA NOT FOUND
            return redirect()
                ->route('admin.usergroup.list')
                ->with('error', lang('#item not found, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        // GET DATA (RULES)
        $rules = SysRule::leftJoin('sys_modules', 'sys_rules.module_id', 'sys_modules.id')
            ->where('sys_modules.status', 1)
            ->select('sys_rules.id', 'sys_rules.name', 'sys_rules.description', 'sys_rules.module_id', 'sys_modules.name as module', 'sys_rules.status', 'sys_rules.updated_at', 'sys_rules.created_at')
            ->orderBy('sys_modules.name')
            ->orderBy('sys_rules.name')
            ->get();
        $params_child = ['id', 'name', 'module_id', 'description'];
        $rules = Helper::generate_parent_child_data($rules, 'module', $params_child);

        // GET DATA (ACCESS)
        $access = [];
        $get_access = SysGroupRule::where('group_id', $id)->get();
        foreach ($get_access as $item) {
            $access[] = $item->rule_id;
        }

        // GET DATA (DIVISION)
        $divisions = SysBranch::select('sys_branches.*', 'sys_divisions.name as division_name')
            ->leftJoin('sys_divisions', 'sys_branches.division_id', '=', 'sys_divisions.id')
            ->orderBy('sys_divisions.name')
            ->orderBy('sys_branches.name');
        // GET ONLY ALLOWED DIVISION
        if (count($allowed_divisions) > 0) {
            $divisions->where(function ($query) use ($allowed_divisions) {
                foreach ($allowed_divisions as $item) {
                    $query->orWhere('sys_divisions.name', '=', $item);
                }
            });
        }
        $divisions = $divisions->get();
        $params_child = ['id', 'name', 'division_id'];
        $divisions = Helper::generate_parent_child_data($divisions, 'division_name', $params_child);

        // GET ACCESS BRANCH
        $division_allowed = [];
        $get_division_allowed = SysGroupBranch::where('group', $id)->get();
        foreach ($get_division_allowed as $item) {
            $division_allowed[] = $item->branch;
        }

        return view('admin.system.usergroup.form', compact('data', 'rules', 'access', 'divisions', 'division_allowed'));
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
            'name' => 'required'
        ];
        $message = [
            'required' => ':attribute ' . lang('field is required', $this->translation)
        ];
        $names = [
            'name' => ucwords(lang('name', $this->translation))
        ];
        $this->validate($request, $validation, $message, $names);

        // HELPER VALIDATION FOR PREVENT SQL INJECTION & XSS ATTACK
        $name = Helper::validate_input_text($request->name);
        if (!$name) {
            return back()
                ->withInput()
                ->with('error', lang('Invalid format for #item', $this->translation, ['#item' => ucwords(lang('name', $this->translation))]));
        }

        // GET THE DATA BASED ON ID
        $data = SysGroup::find($id);

        // CHECK IS DATA FOUND
        if (!$data) {
            // DATA NOT FOUND
            return back()
                ->withInput()
                ->with('error', lang('#item not found, please reload your page before resubmit', $this->translation, ['#item' => $this->item]));
        }

        // UPDATE THE DATA
        $data->name = $name;

        if ($data->save()) {
            /* SET USERGROUP'S ACCESS */
            $group_id = $data->id;
            // DELETE OLD DATA
            SysGroupRule::where('group_id', $group_id)->delete();
            // THEN INSERT NEW DATA
            if (isset($request->access)) {
                $data = [];
                foreach ($request->access as $item) {
                    $data[] = array('group_id' => $group_id, 'rule_id' => $item);
                }
                SysGroupRule::insert($data);
            }

            /* SET USERGROUP'S BRANCH */
            // DELETE OLD DATA
            SysGroupBranch::where('group', $group_id)->delete();
            // THEN INSERT NEW DATA
            if (isset($request->branch)) {
                $data = [];
                foreach ($request->branch as $item) {
                    $data[] = array('group' => $group_id, 'branch' => $item);
                }
                SysGroupBranch::insert($data);
            }

            // LOGGING
            $log = new Syslog();
            $log->subject = Session::get('admin')->id;
            $log->action = 22;
            $log->object = $group_id;
            $log->save();

            // SUCCESS
            return redirect()
                ->route('admin.usergroup.edit', $id)
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
                ->route('admin.usergroup.list')
                ->with('error', lang('#item ID is invalid, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        // GET THE DATA BASED ON ID
        $data = SysGroup::find($id);

        // CHECK IS DATA FOUND
        if (!$data) {
            // DATA NOT FOUND
            return redirect()
                ->route('admin.usergroup.list')
                ->with('error', lang('#item not found, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        // DELETE THE DATA
        if ($data->delete()) {
            // LOGGING
            $log = new Syslog();
            $log->subject = Session::get('admin')->id;
            $log->action = 23;
            $log->object = $data->id;
            $log->save();

            // SUCCESS
            return redirect()
                ->route('admin.usergroup.list')
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

        return view('admin.system.usergroup.list');
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

        // AUTHORIZING DIVISIONS & BRANCHES...
        $allowed_divisions = [];
        $allowed_branches = [];
        $allowed = Helper::get_authorized_branches();
        if ($allowed != 'all') {
            $allowed_divisions = $allowed['allowed_divisions'];
            $allowed_branches = $allowed['allowed_branches'];
        }

        // GET THE DATA
        $query = SysGroup::onlyTrashed()
            ->select(
                'sys_groups.id',
                'sys_groups.name',
                'sys_groups.status',
                'sys_groups.created_at',
                'sys_groups.deleted_at',
                DB::raw('group_concat(DISTINCT `d`.`name`) as division_name')
            )
            ->leftJoin('sys_group_branch as b', 'b.group', 'sys_groups.id')
            ->leftJoin('sys_branches as c', 'c.id', 'b.branch')
            ->leftJoin('sys_divisions as d', 'd.id', 'c.division_id')
            ->where('sys_groups.id', '>', 1)
            ->groupBy(
                'sys_groups.id',
                'sys_groups.name',
                'sys_groups.status',
                'sys_groups.created_at',
                'sys_groups.deleted_at'
            );
        // get only allowed divisions
        if (count($allowed_divisions) > 0) {
            $query->where(function ($query_add) use ($allowed_divisions) {
                foreach ($allowed_divisions as $item) {
                    $query_add->orWhere('d.id', '=', $item);
                }
            });
        }
        // get only allowed branches
        if (count($allowed_branches) > 0) {
            $query->where(function ($query_add) use ($allowed_branches) {
                foreach ($allowed_branches as $item) {
                    $query_add->orWhere('c.id', '=', $item);
                }
            });
        }

        return $datatables->eloquent($query)
            ->addColumn('item_status', function ($data) {
                if ($data->status != 1) {
                    return '<span class="label label-danger"><i>' . ucwords(lang('disabled', $this->translation)) . '</i></span>';
                }
                return '<span class="label label-success">' . ucwords(lang('enabled', $this->translation)) . '</span>';
            })
            ->addColumn('action', function ($data) {
                return '<form action="' . route('admin.usergroup.restore') . '" method="POST" onsubmit="return confirm(\'' . lang('Are you sure to restore this #item?', $this->translation, ['#item' => $this->item]) . '\');" style="display: inline"> ' . csrf_field() . ' <input type="hidden" name="id" value="' . $data->id . '">
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
                ->route('admin.usergroup.deleted')
                ->with('error', lang('#item ID is invalid, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        // GET THE DATA BASED ON ID
        $data = SysGroup::onlyTrashed()->find($id);

        // CHECK IS DATA FOUND
        if (!$data) {
            // DATA NOT FOUND
            return redirect()
                ->route('admin.usergroup.deleted')
                ->with('error', lang('#item not found, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        // RESTORE THE DATA
        if ($data->restore()) {
            // LOGGING
            $log = new Syslog();
            $log->subject = Session::get('admin')->id;
            $log->action = 24;
            $log->object = $data->id;
            $log->save();

            // SUCCESS
            return redirect()
                ->route('admin.usergroup.deleted')
                ->with('success', lang('Successfully restored #item : #name', $this->translation, ['#item' => $this->item, '#name' => $data->name]));
        }

        // FAILED
        return back()
            ->with('error', lang('Oops, failed to restore #item. Please try again.', $this->translation, ['#item' => $this->item]));
    }
}
