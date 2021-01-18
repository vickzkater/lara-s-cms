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
use App\Models\system\SysUser;
use App\Models\system\SysGroup;
use App\Models\system\SysUserGroup;
use App\Models\system\SysLanguage;

class UserController extends Controller
{
    // SET THIS MODULE
    private $module = 'User';
    // SET THIS OBJECT/ITEM NAME
    private $item = 'admin';

    public function list()
    {
        // AUTHORIZING...
        $authorize = Helper::authorizing($this->module, 'View List');
        if ($authorize['status'] != 'true') {
            return back()->with('error', $authorize['message']);
        }

        // FOR DISPLAY ACTIVE DATA
        $data = true;

        return view('admin.system.user.list', compact('data'));
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
        $query = SysUser::select(
            'sys_users.id',
            'sys_users.username',
            'sys_users.email',
            'sys_users.name',
            'sys_users.status',
            'sys_users.created_at',
            'sys_users.updated_at',
            'sys_users.deleted_at',
            DB::raw('GROUP_CONCAT(DISTINCT sys_groups.name) AS groupname'),
            DB::raw('GROUP_CONCAT(DISTINCT d.name) AS division_name')
        )
            ->leftJoin('sys_user_group', 'sys_user_group.user', 'sys_users.id')
            ->leftJoin('sys_groups', 'sys_groups.id', 'sys_user_group.group')
            ->leftJoin('sys_group_branch as b', 'b.group', 'sys_groups.id')
            ->leftJoin('sys_branches as c', 'c.id', 'b.branch')
            ->leftJoin('sys_divisions as d', 'd.id', 'c.division_id')
            ->where('sys_users.id', '>', 1)
            ->groupBy(
                'sys_users.id',
                'sys_users.username',
                'sys_users.email',
                'sys_users.name',
                'sys_users.status',
                'sys_users.created_at',
                'sys_users.updated_at',
                'sys_users.deleted_at'
            );
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
                    $confirm = lang('Are you sure to enable this #item?', $this->translation, ['#item' => $this->item]);
                    return '<button class="btn btn-xs btn-danger" title="' . ucwords(lang('disabled', $this->translation)) . '" onclick="if(confirm(\'' . $confirm . '\')) window.location.replace(\'' . route('admin.user.enable', $data->id) . '\');">' . ucwords(lang('disabled', $this->translation)) . '</button>';
                }
                $confirm = lang('Are you sure to disable this #item?', $this->translation, ['#item' => $this->item]);
                return '<button class="btn btn-xs btn-success" title="' . ucwords(lang('enabled', $this->translation)) . '" onclick="if(confirm(\'' . $confirm . '\')) window.location.replace(\'' . route('admin.user.disable', $data->id) . '\');">' . ucwords(lang('enabled', $this->translation)) . '</button>';
            })
            ->addColumn('action', function ($data) {
                $html = '<a href="' . route('admin.user.edit', $data->id) . '" class="btn btn-xs btn-primary" title="' . ucwords(lang('edit', $this->translation)) . '"><i class="fa fa-pencil"></i>&nbsp; ' . ucwords(lang('edit', $this->translation)) . '</a>';

                $html .= '<form action="' . route('admin.user.delete') . '" method="POST" onsubmit="return confirm(\'' . lang('Are you sure to delete this #item?', $this->translation, ['#item' => $this->item]) . '\');" style="display: inline"> ' . csrf_field() . ' <input type="hidden" name="id" value="' . $data->id . '">
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

        $usergroups = SysGroup::where('status', 1)
            ->where('id', '>', 1)
            ->get();

        return view('admin.system.user.form', compact('usergroups'));
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
            'name' => 'required',
            'username' => 'required|unique:sys_users,username',
            'email' => 'required|email|unique:sys_users,email',
            'password' => 'required|confirmed',
            'usergroup' => 'required|integer'
        ];
        $message = [
            'required' => ':attribute ' . lang('field is required', $this->translation),
            'unique' => ':attribute ' . lang('has already been taken, please input another data', $this->translation)
        ];
        $names = [
            'name' => ucwords(lang('name', $this->translation)),
            'username' => ucwords(lang('username', $this->translation)),
            'email' => ucwords(lang('email', $this->translation)),
            'password' => ucwords(lang('password', $this->translation)),
            'usergroup' => ucwords(lang('usergroup', $this->translation))
        ];
        $this->validate($request, $validation, $message, $names);

        // HELPER VALIDATION FOR PREVENT SQL INJECTION & XSS ATTACK
        $name = Helper::validate_input_text($request->name);
        if (!$name) {
            return back()
                ->withInput()
                ->with('error', lang('Invalid format for #item', $this->translation, ['#item' => ucwords(lang('name', $this->translation))]));
        }
        $username = Helper::validate_input_word($request->username);
        if (!$username) {
            return back()
                ->withInput()
                ->with('error', lang('Invalid format for #item', $this->translation, ['#item' => ucwords(lang('username', $this->translation))]));
        }
        $email = Helper::validate_input_email($request->email);
        if (!$email) {
            return back()
                ->withInput()
                ->with('error', lang('Invalid format for #item', $this->translation, ['#item' => ucwords(lang('email', $this->translation))]));
        }
        $usergroup = (int) $request->usergroup;
        if ($usergroup < 1) {
            return back()
                ->withInput()
                ->with('error', lang('#item must be chosen at least one', $this->translation, ['#item' => ucwords(lang('usergroup', $this->translation))]));
        }
        $status = (int) $request->status;

        // SAVE THE DATA
        $data = new SysUser();
        $data->name = $name;
        $data->username = $username;
        $data->email = $email;
        $data->password = Helper::hashing_this($request->input('password'));
        $data->status = $status;

        if ($data->save()) {
            // SET USERGROUP
            $group = new SysUserGroup();
            $group->user = $data->id;
            $group->group = $usergroup;
            $group->save();

            // LOGGING
            $log = new SysLog();
            $log->subject = Session::get('admin')->id;
            $log->action = 4;
            $log->object = $data->id;
            $log->save();

            // SUCCESS
            return redirect()
                ->route('admin.user.list')
                ->with('success', lang('Successfully added a new #item : #name', $this->translation, ['#item' => $this->item, '#name' => $username]));
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
                ->route('admin.user.list')
                ->with('error', lang('#item ID is invalid, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        // GET THE DATA BASED ON ID
        $data = SysUser::leftJoin('sys_user_group', 'sys_users.id', 'sys_user_group.user')
            ->leftJoin('sys_groups', 'sys_groups.id', 'sys_user_group.group')
            ->select('sys_users.*', 'sys_groups.id as usergroup')
            ->where('sys_users.id', $id)
            ->first();

        // CHECK IS DATA FOUND
        if (!$data) {
            // DATA NOT FOUND
            return redirect()
                ->route('admin.user.list')
                ->with('error', lang('#item not found, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        $usergroups = SysGroup::where('status', 1)
            ->where('id', '>', 1)
            ->get();

        return view('admin.system.user.form', compact('data', 'usergroups'));
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
                ->route('admin.user.list')
                ->with('error', lang('#item ID is invalid, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        // CANNOT CHANGE SUPERADMIN USER, EXCEPT HIMSELF
        if ($id == 1 && Session::get('admin')->id != $id) {
            return redirect()
                ->route('admin.user.list')
                ->with('error', lang('Superadmin user can only be changed by himself', $this->translation));
        }

        // LARAVEL VALIDATION
        $validation = [
            'name' => 'required',
            'email' => 'required|email',
            'usergroup' => 'required|integer'
        ];
        // IF PASSWORD IS CHANGED
        if ($request->input('password')) {
            $validation['password'] = 'required|confirmed';
        }
        $message = [
            'required' => ':attribute ' . lang('field is required', $this->translation)
        ];
        $names = [
            'name' => ucwords(lang('name', $this->translation)),
            'email' => ucwords(lang('email', $this->translation)),
            'password' => ucwords(lang('password', $this->translation)),
            'usergroup' => ucwords(lang('usergroup', $this->translation))
        ];
        $this->validate($request, $validation, $message, $names);

        // HELPER VALIDATION FOR PREVENT SQL INJECTION & XSS ATTACK
        $name = Helper::validate_input_text($request->name);
        if (!$name) {
            return back()
                ->withInput()
                ->with('error', lang('Invalid format for #item', $this->translation, ['#item' => ucwords(lang('name', $this->translation))]));
        }
        $email = Helper::validate_input_email($request->email);
        if (!$email) {
            return back()
                ->withInput()
                ->with('error', lang('Invalid format for #item', $this->translation, ['#item' => ucwords(lang('email', $this->translation))]));
        }
        $usergroup = (int) $request->usergroup;
        if ($usergroup < 1) {
            return back()
                ->withInput()
                ->with('error', lang('#item must be chosen at least one', $this->translation, ['#item' => ucwords(lang('usergroup', $this->translation))]));
        }
        $status = (int) $request->status;

        // GET THE DATA BASED ON ID
        $data = SysUser::find($id);

        // CHECK IS DATA FOUND
        if (!$data) {
            // DATA NOT FOUND
            return back()
                ->withInput()
                ->with('error', lang('#item not found, please reload your page before resubmit', $this->translation, ['#item' => $this->item]));
        }

        // UPDATE THE DATA
        $data->name = $name;
        $data->email = $email;
        $data->status = $status;
        // IF PASSWORD IS CHANGED
        if ($request->input('password')) {
            $data->password = Helper::hashing_this($request->input('password'));
        }

        if ($data->save()) {
            // SET USERGROUP - DELETE OLD DATA FIRST
            SysUserGroup::where('user', $id)->delete();
            // INSERT NEW DATA
            $group = new SysUserGroup();
            $group->user = $id;
            $group->group = $usergroup;
            $group->save();

            // LOGGING
            $log = new SysLog();
            $log->subject = Session::get('admin')->id;
            $log->action = 5;
            $log->object = $data->id;
            $log->save();

            // SUCCESS
            return redirect()
                ->route('admin.user.edit', $id)
                ->with('success', lang('Successfully updated #item : #name', $this->translation, ['#item' => $this->item, '#name' => $data->username]));
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

        $id = (int) $request->id;

        // CHECK OBJECT ID
        if ((int) $id < 1) {
            // INVALID OBJECT ID
            return redirect()
                ->route('admin.user.list')
                ->with('error', lang('#item ID is invalid, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        // SUPERADMIN CANNOT BE DELETED
        if ((int) $id == 1) {
            return redirect()
                ->route('admin.user.list')
                ->with('error', lang('Superadmin cannot be deleted', $this->translation));
        }

        // GET THE DATA BASED ON ID
        $data = SysUser::find($id);

        // CHECK IS DATA FOUND
        if (!$data) {
            // DATA NOT FOUND
            return redirect()
                ->route('admin.user.list')
                ->with('error', lang('#item not found, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        // DELETE THE DATA
        if ($data->delete()) {
            // LOGGING
            $log = new SysLog();
            $log->subject = Session::get('admin')->id;
            $log->action = 6;
            $log->object = $data->id;
            $log->save();

            // SUCCESS
            return redirect()
                ->route('admin.user.list')
                ->with('success', lang('Successfully deleted #item : #name', $this->translation, ['#item' => $this->item, '#name' => $data->username]));
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

        return view('admin.system.user.list');
    }

    public function get_data_deleted(Datatables $datatables, Request $request)
    {
        // AUTHORIZING...
        $authorize = Helper::authorizing($this->module, 'Restore');
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
        $query = SysUser::onlyTrashed()->select(
            'sys_users.id',
            'sys_users.username',
            'sys_users.email',
            'sys_users.name',
            'sys_users.status',
            'sys_users.created_at',
            'sys_users.updated_at',
            'sys_users.deleted_at',
            DB::raw('GROUP_CONCAT(DISTINCT sys_groups.name) AS groupname'),
            DB::raw('GROUP_CONCAT(DISTINCT d.name) AS division_name')
        )
            ->leftJoin('sys_user_group', 'sys_user_group.user', 'sys_users.id')
            ->leftJoin('sys_groups', 'sys_groups.id', 'sys_user_group.group')
            ->leftJoin('sys_group_branch as b', 'b.group', 'sys_groups.id')
            ->leftJoin('sys_branches as c', 'c.id', 'b.branch')
            ->leftJoin('sys_divisions as d', 'd.id', 'c.division_id')
            ->where('sys_users.id', '>', 1)
            ->groupBy(
                'sys_users.id',
                'sys_users.username',
                'sys_users.email',
                'sys_users.name',
                'sys_users.status',
                'sys_users.created_at',
                'sys_users.updated_at',
                'sys_users.deleted_at'
            );
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
                return '<form action="' . route('admin.user.restore') . '" method="POST" onsubmit="return confirm(\'' . lang('Are you sure to restore this #item?', $this->translation, ['#item' => $this->item]) . '\');" style="display: inline"> ' . csrf_field() . ' <input type="hidden" name="id" value="' . $data->id . '">
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

        $id = (int) $request->id;

        // CHECK OBJECT ID
        if ((int) $id < 1) {
            // INVALID OBJECT ID
            return redirect()
                ->route('admin.user.deleted')
                ->with('error', lang('#item ID is invalid, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        // GET THE DATA BASED ON ID
        $data = SysUser::onlyTrashed()->find($id);

        // CHECK IS DATA FOUND
        if (!$data) {
            // DATA NOT FOUND
            return redirect()
                ->route('admin.user.deleted')
                ->with('error', lang('#item not found, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        // RESTORE THE DATA
        if ($data->restore()) {
            // LOGGING
            $log = new SysLog();
            $log->subject = Session::get('admin')->id;
            $log->action = 7;
            $log->object = $data->id;
            $log->save();

            // SUCCESS
            return redirect()
                ->route('admin.user.deleted')
                ->with('success', lang('Successfully restored #item : #name', $this->translation, ['#item' => $this->item, '#name' => $data->username]));
        }

        // FAILED
        return back()
            ->with('error', lang('Oops, failed to restore #item. Please try again.', $this->translation, ['#item' => $this->item]));
    }

    public function profile()
    {
        $data = SysUser::where('id', Session::get('admin')->id)
            ->select('id', 'username', 'email', 'name', 'status')
            ->first();

        return view('admin.system.profile', compact('data'));
    }

    public function profile_edit(Request $request)
    {
        // LARAVEL VALIDATION
        $validation = [
            'name' => 'required',
            'email' => 'required|email'
        ];
        // IF PASSWORD IS CHANGED
        $changepass = false;
        if (!empty($request->input('current_pass')) && !empty($request->input('password'))) {
            $changepass = true;
            $validation['current_pass'] = 'required';
            $validation['password'] = 'required|confirmed';
        }
        $message = [
            'required' => ':attribute ' . lang('field is required', $this->translation),
            'confirmed' => ':attribute ' . lang('confirmation does not match', $this->translation)
        ];
        $names      = [
            'name' => ucwords(lang('name', $this->translation)),
            'email' => ucwords(lang('email', $this->translation)),
            'current_pass' => ucwords(lang('current #item', $this->translation, ['#item' => ucwords(lang('password', $this->translation))])),
            'password' => ucwords(lang('new #item', $this->translation, ['#item' => ucwords(lang('password', $this->translation))])),
        ];
        $this->validate($request, $validation, $message, $names);

        // HELPER VALIDATION FOR PREVENT SQL INJECTION & XSS ATTACK
        $name = Helper::validate_input_text($request->name);
        if (!$name) {
            return back()
                ->withInput()
                ->with('error', lang('Invalid format for #item', $this->translation, ['#item' => ucwords(lang('name', $this->translation))]));
        }
        $email = Helper::validate_input_email($request->email);
        if (!$email) {
            return back()
                ->withInput()
                ->with('error', lang('Invalid format for #item', $this->translation, ['#item' => ucwords(lang('email', $this->translation))]));
        }

        // GET DATA BASED ON SESSION
        $data = SysUser::find(Session::get('admin')->id);

        // UPDATE THE DATA
        $data->name = $name;
        $data->email = $email;

        // IF PASSWORD IS CHANGED
        if ($changepass) {
            if ($data->password != Helper::hashing_this($request->input('current_pass'))) {
                return back()
                    ->withInput()
                    ->with('error', lang('#item is wrong', $this->translation, ['#item' => ucwords(lang('current password', $this->translation))]));
            }
            // UPDATE THE PASSWORD
            $data->password = Helper::hashing_this($request->input('password'));
        }

        if ($data->save()) {
            // LOGGING
            $log = new SysLog();
            $log->subject = Session::get('admin')->id;
            $log->action = 3;
            $log->save();

            if ($changepass) {
                // LOGGING
                $log = new SysLog();
                $log->subject = Session::get('admin')->id;
                $log->action = 8;
                $log->save();
            }

            // SUCCESS
            return redirect()
                ->route('admin.profile')
                ->with('success', lang('#item has been successfully updated', $this->translation, ['#item' => ucwords(lang('profile', $this->translation))]));
        }

        // FAILED
        return back()
            ->withInput()
            ->with('error', lang('Oops, failed to update #item. Please try again.', $this->translation, ['#item' => ucwords(lang('profile', $this->translation))]));
    }

    public function change_language($alias)
    {
        $get_language = SysLanguage::where('alias', $alias)->first();

        if ($get_language) {
            Session::put('language', $get_language->alias);
        } else {
            Session::put('language', env('DEFAULT_LANGUAGE', 'EN'));
        }

        return redirect()->back();
    }

    public function enable($id)
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
                ->route('admin.user.list')
                ->with('error', lang('#item ID is invalid, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        // CANNOT CHANGE SUPERADMIN USER, EXCEPT HIMSELF
        if ($id == 1) {
            return redirect()
                ->route('admin.user.list')
                ->with('error', lang('Superadmin user can only be changed by himself', $this->translation));
        }

        // GET THE DATA BASED ON ID
        $data = SysUser::find($id);

        // CHECK IS DATA FOUND
        if (!$data) {
            // DATA NOT FOUND
            return back()
                ->withInput()
                ->with('error', lang('#item not found, please reload your page before resubmit', $this->translation, ['#item' => $this->item]));
        }

        // UPDATE THE DATA
        $data->status = 1;

        if ($data->save()) {
            // LOGGING
            $log = new SysLog();
            $log->subject = Session::get('admin')->id;
            $log->action = 5;
            $log->object = $data->id;
            $log->save();

            // SUCCESS
            return redirect()
                ->route('admin.user.list')
                ->with('success', lang('Successfully enabled #item : #name', $this->translation, ['#item' => $this->item, '#name' => $data->username]));
        }

        // FAILED
        return back()
            ->withInput()
            ->with('error', lang('Oops, failed to enable #item. Please try again.', $this->translation, ['#item' => $this->item]));
    }

    public function disable($id)
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
                ->route('admin.user.list')
                ->with('error', lang('#item ID is invalid, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        // CANNOT CHANGE SUPERADMIN USER, EXCEPT HIMSELF
        if ($id == 1) {
            return redirect()
                ->route('admin.user.list')
                ->with('error', lang('Superadmin user can only be changed by himself', $this->translation));
        }

        // GET THE DATA BASED ON ID
        $data = SysUser::find($id);

        // CHECK IS DATA FOUND
        if (!$data) {
            // DATA NOT FOUND
            return back()
                ->withInput()
                ->with('error', lang('#item not found, please reload your page before resubmit', $this->translation, ['#item' => $this->item]));
        }

        // UPDATE THE DATA
        $data->status = 0;

        if ($data->save()) {
            // LOGGING
            $log = new SysLog();
            $log->subject = Session::get('admin')->id;
            $log->action = 5;
            $log->object = $data->id;
            $log->save();

            // SUCCESS
            return redirect()
                ->route('admin.user.list')
                ->with('success', lang('Successfully disabled #item : #name', $this->translation, ['#item' => $this->item, '#name' => $data->username]));
        }

        // FAILED
        return back()
            ->withInput()
            ->with('error', lang('Oops, failed to disable #item. Please try again.', $this->translation, ['#item' => $this->item]));
    }
}
