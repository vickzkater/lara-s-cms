<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

use App\Models\Usergroup;
use App\Models\Rule;
use App\Models\AppAccess;
use App\Models\Branch;
use App\Models\Group_Branch;

// library
use App\Libraries\Helper;

class UsergroupController extends Controller
{
    // set this module
    private $module = 'Usergroup Manager';

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

        $data = Usergroup::select('usergroups.*', DB::raw('group_concat(DISTINCT `d`.`name`) as division_name'))
            ->leftJoin('group_branch as b', 'b.group', 'usergroups.id')
            ->leftJoin('branch as c', 'c.id', 'b.branch')
            ->leftJoin('divisions as d', 'd.id', 'c.division_id')
            ->where('usergroups.isDeleted', 0)
            ->where('usergroups.id', '>', 1)
            ->groupBy('usergroups.id')
            ->orderBy('usergroups.id');

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

        return view ('admin.group_manager.list', compact('data'));
    }

    public function create()
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'Add New');
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

        $rules = Rule::leftJoin('app_module', 'app_rule.module_id', 'app_module.id')
            ->where('app_rule.isDeleted', 0)
            ->where('app_module.status', 1)
            ->select('app_rule.id', 'app_rule.name', 'app_rule.description', 'app_rule.module_id', 'app_module.name as module', 'app_rule.status', 'app_rule.updated_at', 'app_rule.created_at')
            ->orderBy('app_module.name')
            ->orderBy('app_rule.name')
            ->get();
        $params_child = ['id', 'name', 'module_id', 'description'];
        $rules = Helper::generate_parent_child_data($rules, 'module', $params_child);

        $divisions = Branch::leftJoin('divisions', 'divisions.id', 'branch.division_id')
            ->where('branch.isDeleted', 0)
            ->select('branch.*', 'divisions.name as division_name')
            ->orderBy('divisions.name')
            ->orderBy('branch.name');

        // get only allowed division
        if (count($allowed_divisions) > 0)
        {
            $divisions->where(function ($query) use ($allowed_divisions) {
                foreach ($allowed_divisions as $item) {
                    $query->orWhere('divisions.name', '=', $item);
                }
            });
        }
        
        $divisions = $divisions->get();
        $params_child = ['id', 'name', 'division_id'];
        $divisions = Helper::generate_parent_child_data($divisions, 'division_name', $params_child);

        return view('admin.group_manager.form', compact('rules', 'divisions'));
    }

    public function do_create(Request $request)
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'Add New');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

        $validation = [
            'name' => 'required|unique:usergroups,name'
        ];

        $message    = [
            'required' => ':attribute field is required',
            'unique' => ':attribute must be unique - there is same value in database'
        ];

        $names      = [
            'name' => 'Name'
        ];

        $this->validate($request, $validation, $message, $names);

        // validating prevent SQL injection
        $name = Helper::validate_input($request->name);
        if(!$name){
            return back()->withInput()->with('error', 'Name must be using alphabet only');
        }

        $data = new Usergroup();
        $data->name = $name;

        if($data->save()){
            // set usergroup's access
            $group_id = $data->id;
            if(isset($request->access)){
                $data = [];
                foreach ($request->access as $item) {
                    $data[] = array('usergroup_id'=>$group_id, 'rule_id'=> $item);
                }
                AppAccess::insert($data);
            }

            /* set usergroup's branch */
            if(isset($request->branch)){
                $data = [];
                foreach ($request->branch as $item) {
                    $data[] = array('group'=>$group_id, 'branch'=> $item);
                }
                Group_Branch::insert($data);
            }

            return redirect()->route('admin_group_manager')->with('success', 'Successfully added a new usergroup : '.$name);
        }else{
            return back()->withInput()->with('error', 'Oops, failed to add a new usergroup. Please try again.');
        }
    }

    public function edit($id)
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'View Details');
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

        if((int) $id < 1){
            return redirect()->route('admin_group_manager')->with('error', 'Usergroup ID is invalid, please recheck your link again');
        }

        $data = Usergroup::find((int) $id);

        if(!$data){
            return redirect()->route('admin_group_manager')->with('error', 'Usergroup not found, please recheck your link again');
        }

        // get rules
        $rules = Rule::leftJoin('app_module', 'app_rule.module_id', 'app_module.id')
            ->where('app_rule.isDeleted', 0)
            ->where('app_module.status', 1)
            ->select('app_rule.id', 'app_rule.name', 'app_rule.description', 'app_rule.module_id', 'app_module.name as module', 'app_rule.status', 'app_rule.updated_at', 'app_rule.created_at')
            ->orderBy('app_module.name')
            ->orderBy('app_rule.name')
            ->get();

        $params_child = ['id', 'name', 'module_id', 'description'];
        $rules = Helper::generate_parent_child_data($rules, 'module', $params_child);

        // get access
        $access = [];
        $get_access = AppAccess::where('usergroup_id', $id)->get();
        foreach ($get_access as $item) {
            $access[] = $item->rule_id;
        }

        // get branches
        $divisions = Branch::leftJoin('divisions', 'divisions.id', 'branch.division_id')
            ->where('branch.isDeleted', 0)
            ->select('branch.*', 'divisions.name as division_name')
            ->orderBy('divisions.name')
            ->orderBy('branch.name');

        // get only allowed division
        if (count($allowed_divisions) > 0)
        {
            $divisions->where(function ($query) use ($allowed_divisions) {
                foreach ($allowed_divisions as $item) {
                    $query->orWhere('divisions.name', '=', $item);
                }
            });
        }
        
        $divisions = $divisions->get();
        $params_child = ['id', 'name', 'division_id'];
        $divisions = Helper::generate_parent_child_data($divisions, 'division_name', $params_child);

        // get access branch
        $division_allowed = [];
        $get_division_allowed = Group_Branch::where('group', $id)->get();
        foreach ($get_division_allowed as $item) {
            $division_allowed[] = $item->branch;
        }
        
        return view('admin.group_manager.form', compact('data', 'rules', 'access', 'divisions', 'division_allowed'));
    }

    public function do_edit($id, Request $request)
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'Edit');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

        if((int) $id < 1){
            return back()->withInput()->with('error', 'Usergroup ID is invalid, please reload your page before resubmit');
        }

        $validation = [
            'name' => 'required'
        ];

        $message    = [
            'required' => ':attribute field is required',
            'unique' => ':attribute must be unique - there is same value in database'
        ];

        $names      = [
            'name' => 'Name'
        ];

        $this->validate($request, $validation, $message, $names);

        // validating prevent SQL injection
        $name = Helper::validate_input($request->name);
        if(!$name){
            return back()->withInput()->with('error', 'Name must be using alphabet only');
        }

        $data = Usergroup::find($id);

        if(!$data){
            return back()->withInput()->with('error', 'Usergroup no found, please reload your page before resubmit');
        }

        $data->name = $name;
        $data->updated_at = date('Y-m-d H:i:s');

        if($data->save()){
            /* set usergroup's access */
            $group_id = $data->id;
            // delete old data
            AppAccess::where('usergroup_id', $group_id)->delete();
            // then insert new data
            if(isset($request->access)){
                $data = [];
                foreach ($request->access as $item) {
                    $data[] = array('usergroup_id'=>$group_id, 'rule_id'=> $item);
                }
                AppAccess::insert($data);
            }

            /* set usergroup's branch */
            // delete old data
            Group_Branch::where('group', $group_id)->delete();
            // then insert new data
            if(isset($request->branch)){
                $data = [];
                foreach ($request->branch as $item) {
                    $data[] = array('group'=>$group_id, 'branch'=> $item);
                }
                Group_Branch::insert($data);
            }

            return redirect()->route('admin_group_edit', $id)->with('success', 'Successfully updated usergroup details');
        }else{
            return back()->withInput()->with('error', 'Oops, failed to update usergroup details. Please try again.');
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
            return redirect()->route('admin_group_manager')->with('error', 'Usergroup ID is invalid, please try again');
        }

        $data = Usergroup::find($id);

        if(!$data){
            return redirect()->route('admin_group_manager')->with('error', 'Usergroup not found, please try again');
        }

        $data->isDeleted = 1;

        if($data->save()){
            return redirect()->route('admin_group_manager', $id)->with('success', 'Successfully deleted usergroup : '.$data->name);
        }else{
            return back()->with('error', 'Oops, failed to delete usergroup. Please try again.');
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

        $deleted = Usergroup::select('usergroups.*', DB::raw('group_concat(DISTINCT `d`.`name`) as division_name'))
            ->leftJoin('group_branch as b', 'b.group', 'usergroups.id')
            ->leftJoin('branch as c', 'c.id', 'b.branch')
            ->leftJoin('divisions as d', 'd.id', 'c.division_id')
            ->where('usergroups.isDeleted', 1)
            ->where('usergroups.id', '>', 1)
            ->groupBy('usergroups.id')
            ->orderBy('usergroups.id');

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

        return view ('admin.group_manager.list', compact('deleted'));
    }

    public function restore($id)
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'Restore');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

        if((int) $id < 1){
            return redirect()->route('admin_group_manager_deleted')->with('error', 'Usergroup ID is invalid, please try again');
        }

        $data = Usergroup::find($id);

        if(!$data){
            return redirect()->route('admin_group_manager_deleted')->with('error', 'Usergroup not found, please try again');
        }

        $data->isDeleted = 0;

        if($data->save()){
            return redirect()->route('admin_group_manager_deleted', $id)->with('success', 'Successfully restored usergroup : '.$data->name);
        }else{
            return back()->with('error', 'Oops, failed to restore usergroup. Please try again.');
        }
    }
}