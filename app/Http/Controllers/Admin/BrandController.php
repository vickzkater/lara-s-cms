<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;

// library
use App\Libraries\Helper;

class BrandController extends Controller
{
    // set this module
    private $module = 'Brand';

    public function list()
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'View List');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

        $data = Brand::where('isDeleted', 0)
            ->select('id', 'name', 'status', 'updated_at', 'created_at')
            ->orderBy('name')
            ->paginate(10);

        return view ('admin.brand.list', compact('data'));
    }

    public function create()
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'Add New');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

       return view('admin.brand.form');
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
        $status = (int) $request->status;

        $data = new Brand();
        $data->name = $name;
        $data->status = $status;

        if($data->save()){
            return redirect()->route('admin_brand_list')->with('success', 'Successfully added a new brand : '.$name);
        }

        return back()->withInput()->with('error', 'Oops, failed to add a new brand. Please try again.');
    }

    public function edit($id)
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'View Details');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

        if((int) $id < 1){
            return redirect()->route('admin_brand_list')->with('error', 'Brand ID is invalid, please recheck your link again');
        }

        $data = Brand::find($id);

        if(!$data){
            return redirect()->route('admin_brand_list')->with('error', 'Brand not found, please recheck your link again');
        }

        return view('admin.brand.form', compact('data'));
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
        $status = (int) $request->status;

        // get existing data
        $data = Brand::find($id);

        if(!$data){
            return back()->withInput()->with('error', 'Brand not found, please reload your page before resubmit');
        }

        $data->name = $name;
        $data->status = $status;

        if($data->save()){
            return redirect()->route('admin_brand_edit', $id)->with('success', 'Successfully updated brand : '.$name);
        }
        return back()->withInput()->with('error', 'Oops, failed to updated brand. Please try again.');
    }

    public function delete($id)
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'Delete');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

        if((int) $id < 1){
            return redirect()->route('admin_brand_list')->with('error', 'Brand ID is invalid, please try again');
        }

        $data = Brand::find($id);

        if(!$data){
            return redirect()->route('admin_brand_list')->with('error', 'Brand not found, please try again');
        }

        $data->isDeleted = 1;

        if($data->save()){
            return redirect()->route('admin_brand_list', $id)->with('success', 'Successfully deleted brand : '.$data->name);
        }else{
            return back()->with('error', 'Oops, failed to delete brand. Please try again.');
        }
    }

    public function list_deleted()
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'Restore');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

        $deleted = Brand::where('isDeleted', 1)
            ->select('id', 'name', 'status', 'updated_at', 'created_at')
            ->orderBy('name')
            ->paginate(10);

        return view ('admin.brand.list', compact('deleted'));
    }

    public function restore($id)
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'Restore');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

        if((int) $id < 1){
            return redirect()->route('admin_brand_deleted')->with('error', 'Brand ID is invalid, please try again');
        }

        $data = Brand::find($id);

        if(!$data){
            return redirect()->route('admin_brand_deleted')->with('error', 'Brand not found, please try again');
        }

        $data->isDeleted = 0;

        if($data->save()){
            return redirect()->route('admin_brand_deleted', $id)->with('success', 'Successfully restored brand : '.$data->name);
        }else{
            return back()->with('error', 'Oops, failed to restore brand. Please try again.');
        }
    }
}
