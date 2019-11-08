<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Division;
use App\Models\Branch;

// library
use App\Libraries\Helper;

class DivisionController extends Controller
{
    // set this module
    private $module = 'Division';

    public function list()
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'View List');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

        $data = Division::where('isDeleted', 0)
            ->orderBy('name')
            ->paginate(10);

        return view ('admin.division.list', compact('data'));
    }

    public function create()
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'Add New');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

       return view('admin.division.form');
    }

    public function do_create(Request $request)
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'Add New');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

        $validation = [
            'name' => 'required'
        ];

        $message    = [
            'required' => ':attribute field is required'
        ];

        $names      = [
            'name' => 'Name'
        ];

        $this->validate($request, $validation, $message, $names);

        // validating prevent SQL injection
        $name = Helper::validate_input_string($request->name);
        if(!$name){
            return back()->withInput()->with('error', 'Invalid format for Name');
        }
        $description = Helper::validate_input_string($request->description);
        $status = (int) $request->status;

        $data = new Division();
        $data->name = $name;
        $data->description = $description;
        $data->status = $status;

        if($data->save()){
            return redirect()->route('admin_division_list')->with('success', 'Successfully added a new division : '.$name);
        }

        return back()->withInput()->with('error', 'Oops, failed to add a new division. Please try again.');
    }

    public function edit($id)
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'View Details');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

        if((int) $id < 1){
            return redirect()->route('admin_division_list')->with('error', 'Division ID is invalid, please recheck your link again');
        }

        $data = Division::find($id);

        if(!$data){
            return redirect()->route('admin_division_list')->with('error', 'Division not found, please recheck your link again');
        }

        return view('admin.division.form', compact('data'));
    }

    public function do_edit($id, Request $request)
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'Edit');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

        $validation = [
            'name' => 'required'
        ];

        $message    = [
            'required' => ':attribute field is required'
        ];

        $names      = [
            'name' => 'Name'
        ];

        $this->validate($request, $validation, $message, $names);

        // validating prevent SQL injection
        $name = Helper::validate_input_string($request->name);
        if(!$name){
            return back()->withInput()->with('error', 'Invalid format for Name');
        }
        $description = Helper::validate_input_string($request->description);
        $status = (int) $request->status;

        // get existing data
        $data = Division::find($id);

        if(!$data){
            return back()->withInput()->with('error', 'Division not found, please reload your page before resubmit');
        }

        $data->name = $name;
        $data->description = $description;
        $data->status = $status;

        if($data->save()){
            return redirect()->route('admin_division_edit', $id)->with('success', 'Successfully updated division : '.$name);
        }
        return back()->withInput()->with('error', 'Oops, failed to updated division. Please try again.');
    }

    public function delete($id)
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'Delete');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

        if((int) $id < 1){
            return redirect()->route('admin_division_list')->with('error', 'Division ID is invalid, please try again');
        }

        $data = Division::find($id);

        if(!$data){
            return redirect()->route('admin_division_list')->with('error', 'Division not found, please try again');
        }

        $data->isDeleted = 1;

        if($data->save()){
            return redirect()->route('admin_division_list')->with('success', 'Successfully deleted division : '.$data->name);
        }else{
            return back()->with('error', 'Oops, failed to delete division. Please try again.');
        }
    }

    public function list_deleted()
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'Restore');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

        $deleted = Division::where('isDeleted', 1)
            ->orderBy('name')
            ->paginate(10);

        return view ('admin.division.list', compact('deleted'));
    }

    public function restore($id)
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'Restore');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

        if((int) $id < 1){
            return redirect()->route('admin_division_deleted')->with('error', 'Division ID is invalid, please try again');
        }

        $data = Division::find($id);

        if(!$data){
            return redirect()->route('admin_division_deleted')->with('error', 'Division not found, please try again');
        }

        $data->isDeleted = 0;

        if($data->save()){
            return redirect()->route('admin_division_deleted')->with('success', 'Successfully restored division : '.$data->name);
        }else{
            return back()->with('error', 'Oops, failed to restore division. Please try again.');
        }
    }
}
