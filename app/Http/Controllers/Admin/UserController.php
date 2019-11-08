<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

use App\Models\User;
use App\Models\Usergroup;
use App\Models\User_Group;
use App\Models\LogsSystem;
use App\Models\Language;

// library
use App\Libraries\Helper;

class UserController extends Controller
{
    // set this module
    private $module = 'User Manager';

    public function profile()
    {
        $data = User::where('id', Session::get('admin')->id)
            ->select('id', 'username', 'email', 'name', 'status')
            ->first();

        return view ('admin.profile', compact('data'));
    }

    public function profile_edit(Request $request)
    {
        $validation = [
            'name' => 'required',
            'email' => 'required|email'
        ];

        // if password is changed
        $changepass = false;
        if(!empty($request->input('current_pass')) && !empty($request->input('password'))){
            $changepass = true;
            $validation['current_pass'] = 'required|min:8';
            $validation['password'] = 'required|min:8|confirmed';
        }

        $message    = [
            'required' => ':attribute should not be empty',
            'min' => ':attribute must be minimal :min characters'
        ];

        $names      = [
            'name' => 'Name',
            'email' => 'Email',
            'current_pass' => 'Current Password',
            'password' => 'New Password',
        ];

        $this->validate($request, $validation, $message, $names);

        // validating prevent SQL injection
        $name = Helper::validate_input($request->name);
        if(!$name){
            return back()->withInput($request->flashExcept('password'))->with('error', 'Name must be using alphabet only');
        }
        $email = Helper::validate_input_email($request->email);
        if(!$email){
            return back()->withInput($request->flashExcept('password'))->with('error', 'Email must be using format: username@domain.com');
        }

        $data = User::find(Session::get('admin')->id);
        
        $data->name = $name;
        $data->email = $email;

        // if password is changed
        if($changepass){
            if($data->password != Helper::hashing_this($request->input('current_pass'))){
                return back()->withInput($request->flashExcept('password'))->with('error', 'Current Password is wrong');
            }

            $data->password = Helper::hashing_this($request->input('password'));
        }

        if($data->save()){
            // refresh data in session
            Session::put('admin', $data);

            // logging
            $log = new LogsSystem();
            $log->subject = Session::get('admin')->id;
            $log->action = 3;
            $log->save();

            if($changepass){
                // logging
                $log = new LogsSystem();
                $log->subject = Session::get('admin')->id;
                $log->action = 8;
                $log->save();
            }

            return redirect()->route('admin_profile')->with('success', 'Profile has been successfully updated');
        }else{
            return back()->withInput($request->flashExcept('password'))->with('error', 'Oops, profile failed to update. Please try again.');
        }
    }

    public function list()
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'View List');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

        // authorizing division...
        $allowed_divisions = [];
        $sessions = Session::all();
        foreach ($sessions['division'] as $item)
        {
            $authorize_division = Helper::authorizing_division($item);
            if ($authorize_division['status'] == 'true')
            {
                if ($authorize_division['message'] == 'all')
                {
                    break;
                }
                else
                {
                    $allowed_divisions[] = $authorize_division['message'];
                }
            }
            else
            {
                return back()->with('error', $authorize['message']);
            }
        }

        $data = User::leftJoin('user_group', 'user_group.user', 'users.id')
            ->leftJoin('usergroups', 'usergroups.id', 'user_group.group')
            ->leftJoin('group_branch as b', 'b.group', 'usergroups.id')
            ->leftJoin('branch as c', 'c.id', 'b.branch')
            ->leftJoin('divisions as d', 'd.id', 'c.division_id')
            ->where('users.isDeleted', 0)
            ->where('users.id', '>', 1)
            ->select('users.id', 'users.username', 'users.email', 'users.name', DB::raw('group_concat(distinct usergroups.name) as groupname'), 'users.status', 'users.updated_at', DB::raw('group_concat(distinct d.name) as division_name'))
            ->groupBy('users.id')
            ->orderBy('users.username');

        // get only allowed division
        if (count($allowed_divisions) > 0)
        {
            $data->where(function ($query) use ($allowed_divisions) {
                foreach ($allowed_divisions as $item) {
                    $query->orWhere('d.name', '=', $item);
                }
            });
        }

        $data = $data->paginate(10);

        return view ('admin.user_manager.list', compact('data'));
    }

    public function create()
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'Add New');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }
        
        $usergroups = Usergroup::where('isDeleted', 0)->where('id', '>', 1)->get();
        return view('admin.user_manager.form', compact('usergroups'));
    }

    public function do_create(Request $request)
    {
        $authorize = Helper::authorizing($this->module, 'Add New');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

        $validation = [
            'name' => 'required',
            'username' => 'required|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'usergroup' => 'required|integer'
        ];

        $message    = [
            'required' => ':attribute field is required',
            'min' => ':attribute must be minimal :min characters'
        ];

        $names      = [
            'name' => 'Name',
            'name' => 'Name',
            'email' => 'Email',
            'password' => 'Password',
            'usergroup' => 'Usergroup'
        ];

        $this->validate($request, $validation, $message, $names);

        // validating prevent SQL injection
        $name = Helper::validate_input($request->name);
        if(!$name){
            return back()->withInput()->with('error', 'Name must be using alphabet only');
        }
        $username = Helper::validate_input_word($request->username);
        if(!$username){
            return back()->withInput()->with('error', 'Username must be using alphabet only');
        }
        $email = Helper::validate_input_email($request->email);
        if(!$email){
            return back()->withInput()->with('error', 'Email must be using format: username@domain.com');
        }
        $usergroup = (int) $request->usergroup;
        if($usergroup < 0){
            return back()->withInput()->with('error', 'Usergroup must selected at least one');
        }

        $data = new User();
        $data->name = $name;
        $data->username = $username;
        $data->email = $email;
        $data->password = Helper::hashing_this($request->input('password'));

        if($data->save()){
            // set usergroup
            $group = new User_Group();
            $group->user = $data->id;
            $group->group = $usergroup;
            $group->save();

            // logging
            $log = new LogsSystem();
            $log->subject = Session::get('admin')->id;
            $log->action = 4;
            $log->object = $data->id;
            $log->save();

            return redirect()->route('admin_user_manager')->with('success', 'Successfully added a new user : '.$username);
        }else{
            return back()->withInput()->with('error', 'Oops, failed to add a new user. Please try again.');
        }
    }

    public function edit($id)
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'View Details');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

        if((int) $id < 1){
            return redirect()->route('admin_user_manager')->with('error', 'User ID is invalid, please recheck your link again');
        }

        // if edit user details itself
        if(Session::get('admin')->id == $id){
            return redirect()->route('admin_profile');
        }

        $data = User::leftJoin('user_group', 'users.id', 'user_group.user')
            ->leftJoin('usergroups', 'usergroups.id', 'user_group.group')
            ->select('users.*', 'usergroups.id as usergroup')
            ->where('users.id', (int) $id)
            ->first();

        if(!$data){
            return redirect()->route('admin_user_manager')->with('error', 'User not found, please recheck your link again');
        }

        $usergroups = Usergroup::where('isDeleted', 0)->where('id', '>', 1)->get();

        return view('admin.user_manager.form', compact('data', 'usergroups'));
    }

    public function do_edit($id, Request $request)
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'Edit');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

        if((int) $id < 1){
            return back()->withInput($request->flashExcept('password'))->with('error', 'User ID is invalid, please reload your page before resubmit');
        }

        if($id == 1 && Session::get('admin')->id == $id){
            return back()->withInput($request->flashExcept('password'))->with('error', 'Superadmin user can only be changed by himself');
        }

        $validation = [
            'name' => 'required',
            'email' => 'required|email',
            'usergroup' => 'required|integer'
        ];

        // if password is changed
        if($request->input('password')){
            $validation['password'] = 'required|min:8|confirmed';
        }

        $message    = [
            'required' => ':attribute field is required',
            'min' => ':attribute must be minimal :min characters',
        ];

        $names      = [
            'name' => 'Name',
            'email' => 'Email',
            'password' => 'Password',
            'usergroup' => 'Usergroup'
        ];

        $this->validate($request, $validation, $message, $names);

        // validating prevent SQL injection
        $name = Helper::validate_input($request->name);
        if(!$name){
            return back()->withInput($request->flashExcept('password'))->with('error', 'Name must be using alphabet only');
        }
        $email = Helper::validate_input_email($request->email);
        if(!$email){
            return back()->withInput($request->flashExcept('password'))->with('error', 'Email must be using format: username@domain.com');
        }
        $usergroup = (int) $request->usergroup;
        if($usergroup < 0){
            return back()->withInput()->with('error', 'Usergroup must selected at least one');
        }

        $data = User::find($id);

        if(!$data){
            return back()->withInput($request->flashExcept('password'))->with('error', 'User no found, please reload your page before resubmit');
        }

        $data->name = $name;
        $data->email = $email;
        $data->status = $request->input('status');

        // if password is changed
        if($request->input('password')){
            $data->password = Helper::hashing_this($request->input('password'));
        }

        if($data->save()){
            // set usergroup
            // delete old data first
            User_Group::where('user', $id)->delete();
            // insert new data
            $group = new User_Group();
            $group->user = $id;
            $group->group = $usergroup;
            $group->save();

            // logging
            $log = new LogsSystem();
            $log->subject = Session::get('admin')->id;
            $log->action = 5;
            $log->object = $data->id;
            $log->save();

            return redirect()->route('admin_user_edit', $id)->with('success', 'Successfully updated user details');
        }else{
            return back()->withInput($request->flashExcept('password'))->with('error', 'Oops, failed to update user details. Please try again.');
        }
    }

    public function delete($id)
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'Delete');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

        if((int) $id < 1){
            return redirect()->route('admin_user_manager')->with('error', 'User ID is invalid, please try again');
        }

        if((int) $id == 1){
            return redirect()->route('admin_user_manager')->with('error', 'Superadmin cannot be deleted');
        }

        $data = User::find($id);

        if(!$data){
            return redirect()->route('admin_user_manager')->with('error', 'User not found, please try again');
        }

        $data->isDeleted = 1;

        if($data->save()){
            // logging
            $log = new LogsSystem();
            $log->subject = Session::get('admin')->id;
            $log->action = 6;
            $log->object = $data->id;
            $log->save();

            return redirect()->route('admin_user_manager', $id)->with('success', 'Successfully deleted user : '.$data->username);
        }else{
            return back()->with('error', 'Oops, failed to delete user. Please try again.');
        }
    }

    public function list_deleted()
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'Restore');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

        // authorizing division...
        $allowed_divisions = [];
        $sessions = Session::all();
        foreach ($sessions['division'] as $item)
        {
            $authorize_division = Helper::authorizing_division($item);
            if ($authorize_division['status'] == 'true')
            {
                if ($authorize_division['message'] == 'all')
                {
                    break;
                }
                else
                {
                    $allowed_divisions[] = $authorize_division['message'];
                }
            }
            else
            {
                return back()->with('error', $authorize['message']);
            }
        }

        $deleted = User::leftJoin('user_group', 'user_group.user', 'users.id')
            ->leftJoin('usergroups', 'usergroups.id', 'user_group.group')
            ->leftJoin('group_branch as b', 'b.group', 'usergroups.id')
            ->leftJoin('branch as c', 'c.id', 'b.branch')
            ->leftJoin('divisions as d', 'd.id', 'c.division_id')
            ->where('users.isDeleted', 1)
            ->where('users.id', '>', 1)
            ->select('users.id', 'users.username', 'users.email', 'users.name', DB::raw('group_concat(distinct usergroups.name) as groupname'), 'users.status', 'users.updated_at', DB::raw('group_concat(distinct d.name) as division_name'))
            ->groupBy('users.id')
            ->orderBy('users.username');

        // get only allowed division
        if (count($allowed_divisions) > 0)
        {
            $deleted->where(function ($query) use ($allowed_divisions) {
                foreach ($allowed_divisions as $item) {
                    $query->orWhere('d.name', '=', $item);
                }
            });
        }

        $deleted = $deleted->paginate(10);
        
        return view ('admin.user_manager.list', compact('deleted'));
    }

    public function restore($id)
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'Restore');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }
        
        if((int) $id < 1){
            return redirect()->route('admin_user_manager_deleted')->with('error', 'User ID is invalid, please try again');
        }

        $data = User::find($id);

        if(!$data){
            return redirect()->route('admin_user_manager_deleted')->with('error', 'User not found, please try again');
        }

        $data->isDeleted = 0;

        if($data->save()){
            // logging
            $log = new LogsSystem();
            $log->subject = Session::get('admin')->id;
            $log->action = 7;
            $log->object = $data->id;
            $log->save();

            return redirect()->route('admin_user_manager_deleted', $id)->with('success', 'Successfully restored user : '.$data->username);
        }else{
            return back()->with('error', 'Oops, failed to restore user. Please try again.');
        }
    }

    public function change_language($alias)
    {
        $get_language = Language::where('alias', $alias)->first();

        if ($get_language)
        {
            Session::put('language', $get_language->alias);
        }
        else
        {
            Session::put('language', env('DEFAULT_LANGUAGE', 'EN'));
        }

        return redirect()->back();
    }
}