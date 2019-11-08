<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

use App\Models\User;
use App\Models\LogsSystem;
use App\Models\AppAccess;
use App\Models\Group_Branch;

// library
use App\Libraries\Helper;

class AuthController extends Controller
{
    public function login()
    {
        if(Session::get('admin')){
            return redirect()->route('admin_home');
        }
        return view('admin.login');
    }

    public function do_login(Request $request)
    {
        $validation = [
            'login_id' => 'required',
            'login_pass' => 'required|min:8'
        ];

        $message    = [
            'required' => ':attribute '.lang('should not be empty'),
            'login_pass.min' => ':attribute must be minimal :min characters'
        ];

        $names      = [
            'login_id' => 'Username',
            'login_pass' => 'Password'
        ];

        $this->validate($request, $validation, $message, $names);

        $admin = User::leftJoin('user_group', 'users.id', 'user_group.user')
            ->leftJoin('usergroups', 'usergroups.id', 'user_group.group')
            ->where([
                'username' => Helper::validate_input($request->login_id),
                'password' => Helper::hashing_this($request->login_pass)
            ])
            ->select('users.id', 'users.name', 'users.username', 'users.isDeleted', 'users.status', 'usergroups.id as group_id', 'usergroups.name as group_name')
            ->first();
        
        if ($admin) {
            if ($admin->isDeleted != 0) {
                return back()->withInput($request->flashExcept('login_pass'))->with('error', 'Login failed! Because your account has been deleted!');
            }
            
            if($admin->status != 1){
                return back()->withInput($request->flashExcept('login_pass'))->with('error', 'Login failed! Because your account has been disabled!');
            }

            // success login
            // logging
            $log = new LogsSystem();
            $log->subject = $admin->id;
            $log->action = 1;
            $log->save();

            // get user's access
            $access = [];
            $get_access = AppAccess::leftJoin('app_rule', 'app_rule.id', 'app_access.rule_id')
                ->leftJoin('app_module', 'app_rule.module_id', 'app_module.id')
                ->where('usergroup_id', $admin->group_id)
                ->where('app_module.status', 1)
                ->select('app_access.rule_id', 'app_rule.name as rule_name', 'app_rule.description as rule_desc', 'app_module.name as module_name')
                ->get();
            if(count($get_access) > 0){
                foreach ($get_access as $item) {
                    $obj = new \stdClass();
                    $obj->rule_id = $item->rule_id;
                    $obj->rule_name = $item->rule_name;
                    $obj->rule_desc = $item->rule_desc;
                    $obj->module_name = $item->module_name;
                    $access[] = $obj;
                }
            }

            // get user's access branch
            $branch_allowed = [];
            $division_allowed = [];
            $get_branch_allowed = Group_Branch::leftJoin('branch', 'branch.id', 'group_branch.branch')
                ->leftJoin('divisions', 'divisions.id', 'branch.division_id')
                ->where('branch.isDeleted', 0)
                ->where('group_branch.group', $admin->group_id)
                ->select('branch.*', 'divisions.name as division_name', 'divisions.id as division_id')
                ->orderBy('divisions.name')
                ->orderBy('branch.name')
                ->get();
        
            if(count($get_branch_allowed) > 0)
            {
                foreach ($get_branch_allowed as $item) {
                    $obj = new \stdClass();
                    $obj->branch_id = $item->id;
                    $obj->branch = $item->name;
                    $obj->division_id = $item->division_id;
                    $obj->division = $item->division_name;
                    $branch_allowed[] = $obj;

                    if (!in_array($item->division_name, $division_allowed))
                    {
                        $division_allowed[] = $item->division_name;
                    }
                }
            }

            return redirect()
                ->route('admin_home')
                ->with(Session::put('admin', $admin))
                ->with(Session::put('access', $access))
                ->with(Session::put('branch', $branch_allowed))
                ->with(Session::put('division', $division_allowed));
        }else{
            return back()->withInput($request->flashExcept('login_pass'))->with('error', 'Username or Password is wrong!');
        }
    }

    public function logout()
    {
        // logging
        $log = new LogsSystem();
        $log->subject = Session::get('admin')->id;
        $log->action = 2;
        $log->save();

        Session::forget('admin');
        return redirect()->route('admin_login')->with('success', 'Logout successfully');
    }
}
