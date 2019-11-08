<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rule;
use App\Models\Module;

// library
use App\Libraries\Helper;

class RuleController extends Controller
{
    // set this module
    private $module = 'Rule';

    public function list(Request $request)
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'View List');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

        $data = Rule::leftJoin('app_module', 'app_rule.module_id', 'app_module.id')
            ->where('app_rule.isDeleted', 0)
            ->select('app_rule.id', 'app_rule.name', 'app_module.name as module', 'app_rule.status', 'app_rule.updated_at', 'app_rule.created_at')
            ->orderBy('app_module.name')
            ->orderBy('app_rule.name');

        if(isset($request->search) && !empty($request->search)){
            $search = $request->search;
            $data->where(function ($query) use ($search) {
                $query->where('app_module.name', 'LIKE', '%'.$search.'%')
                    ->orWhere('app_rule.name', 'LIKE', '%'.$search.'%');
            });
        }

        $data = $data->paginate(10);

        return view ('admin.rule.list', compact('data'));
    }

    public function create()
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'Add New');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

        $modules = Module::where('status', 1)->where('isDeleted', 0)->orderBy('name')->get();
        return view('admin.rule.form', compact('modules'));
    }

    public function do_create(Request $request)
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'Add New');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

        $validation = [
            'module_id' => 'required|integer'
        ];

        $message    = [
            'required' => ':attribute field is required'
        ];

        $names      = [
            'module_id' => 'Module',
            'name' => 'Name'
        ];

        $this->validate($request, $validation, $message, $names);

        // validating prevent SQL injection
        $module_id = (int) $request->module_id;
        if($module_id < 1){
            return back()->withInput()->with('error', 'Module is required, please choose one');
        }
        $name = Helper::validate_input_string($request->name);
        $description = Helper::validate_input_string($request->description);
        $status = (int) $request->status;

        if (!isset($request->packet))
        {
            $statement = 'a new rule: '.$name;

            $data = new Rule();
            $data->module_id = $module_id;
            $data->name = $name;
            $data->description = $description;
            $data->status = $status;
    
            $result = $data->save();
        }
        else
        {
            $statement = 'a packet new rules';

            $packets = explode('|', $request->packet);
            foreach ($packets as $item) 
            {
                $data = new Rule();
                $data->module_id = $module_id;
                $data->name = $item;
                $data->status = $status;
                
                if ($data->save())
                {
                    $result = true;
                }
                else
                {
                    $result = false;
                    break;
                }
            }
        }

        if ($result)
        {
            if (Helper::validate_input_string($request->input_again) == 'yes')
            {
                return redirect()->route('admin_rule_create')->with('success', 'Successfully added '.$statement);
            }
            return redirect()->route('admin_rule_list')->with('success', 'Successfully added '.$statement);
        }

        return back()->withInput()->with('error', 'Oops, failed to add '.$statement.'. Please try again.');
    }

    public function edit($id)
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'View Details');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

        if((int) $id < 1){
            return redirect()->route('admin_rule_list')->with('error', 'Rule ID is invalid, please recheck your link again');
        }

        $data = Rule::find($id);

        if(!$data){
            return redirect()->route('admin_rule_list')->with('error', 'Rule not found, please recheck your link again');
        }

        $modules = Module::where('status', 1)->where('isDeleted', 0)->get();
        return view('admin.rule.form', compact('data', 'modules'));
    }

    public function do_edit($id, Request $request)
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'Edit');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

        $validation = [
            'module_id' => 'required|integer',
            'name' => 'required'
        ];

        $message    = [
            'required' => ':attribute field is required'
        ];

        $names      = [
            'module_id' => 'Module',
            'name' => 'Name'
        ];

        $this->validate($request, $validation, $message, $names);

        // validating prevent SQL injection
        $module_id = (int) $request->module_id;
        if($module_id < 1){
            return back()->withInput()->with('error', 'Module is required, please choose one');
        }
        $name = Helper::validate_input_string($request->name);
        if(!$name){
            return back()->withInput()->with('error', 'Invalid format for Name');
        }
        $description = Helper::validate_input_string($request->description);
        
        $status = (int) $request->status;

        // get existing data
        $data = Rule::find($id);

        if(!$data){
            return back()->withInput()->with('error', 'Rule not found, please reload your page before resubmit');
        }

        $data->module_id = $module_id;
        $data->name = $name;
        $data->description = $description;
        $data->status = $status;

        if($data->save()){
            return redirect()->route('admin_rule_edit', $id)->with('success', 'Successfully updated this rule');
        }
        return back()->withInput()->with('error', 'Oops, failed to updated rule. Please try again.');
    }

    public function delete($id)
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'Delete');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

        if((int) $id < 1){
            return redirect()->route('admin_rule_list')->with('error', 'Rule ID is invalid, please try again');
        }

        $data = Rule::find($id);

        if(!$data){
            return redirect()->route('admin_rule_list')->with('error', 'Rule not found, please try again');
        }

        $data->isDeleted = 1;

        if($data->save()){
            return redirect()->route('admin_rule_list')->with('success', 'Successfully deleted rule');
        }else{
            return back()->with('error', 'Oops, failed to delete rule. Please try again.');
        }
    }

    public function list_deleted()
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'Restore');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

        $deleted = Rule::leftJoin('app_module', 'app_rule.module_id', 'app_module.id')
            ->where('app_rule.isDeleted', 1)
            ->select('app_rule.id', 'app_rule.name', 'app_module.name as module', 'app_rule.status', 'app_rule.updated_at', 'app_rule.created_at')
            ->orderBy('app_rule.module_id')
            ->orderBy('app_rule.id')
            ->paginate(10);

        return view ('admin.rule.list', compact('deleted'));
    }

    public function restore($id)
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'Restore');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

        if((int) $id < 1){
            return redirect()->route('admin_rule_deleted')->with('error', 'Rule ID is invalid, please try again');
        }

        $data = Rule::find($id);

        if(!$data){
            return redirect()->route('admin_rule_deleted')->with('error', 'Rule not found, please try again');
        }

        $data->isDeleted = 0;

        if($data->save()){
            return redirect()->route('admin_rule_deleted', $id)->with('success', 'Successfully restored rule');
        }else{
            return back()->with('error', 'Oops, failed to restore rule. Please try again.');
        }
    }
}
