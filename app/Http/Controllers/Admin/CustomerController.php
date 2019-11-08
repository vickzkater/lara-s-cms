<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use Session;

// library
use App\Libraries\Helper;

class CustomerController extends Controller
{
    public function list()
    {
        $data = Customer::where('isDeleted', 0)
            ->select('id', 'phone', 'email', 'name', 'status', 'updated_at')
            ->orderBy('name')
            ->paginate(10);

        return view ('admin.customer.list', compact('data'));
    }

    public function create()
    {
       return view('admin.customer.form');
    }

    public function do_create(Request $request)
    {
        $validation = [
            'name' => 'required',
            'phone' => 'required|unique:customers,phone',
            'address' => 'required',
            'status' => 'required|integer'
        ];

        $message    = [
            'required' => ':attribute field is required',
            'min' => ':attribute must be minimal :min characters'
        ];

        $names      = [
            'name' => 'Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'address' => 'Address',
            'status' => 'Status'
        ];

        $this->validate($request, $validation, $message, $names);

        // validating prevent SQL injection
        $name = Helper::validate_input_string($request->name);
        if(!$name){
            return back()->withInput()->with('error', 'Name must be using alphabet only');
        }
        $email = NULL;
        if(isset($request->email)){
            $email = Helper::validate_input_email($request->email);
            if(!$email){
                return back()->withInput()->with('error', 'Email must be using format: username@domain.com');
            }
        }
        $phone = Helper::validate_input_string($request->phone);
        $address = Helper::validate_input_string($request->address);
        if(!$address){
            return back()->withInput()->with('error', 'Address must be using alphanumeric only');
        }

        $data = new Customer();
        $data->name = $name;
        $data->email = $email;
        $data->phone = $phone;
        $data->address = $address;
        $data->status = $request->input('status');

        if($data->save()){
            return redirect()->route('admin_customer_list')->with('success', 'Successfully added a new customer : '.$name);
        }else{
            return back()->withInput()->with('error', 'Oops, failed to add a new customer. Please try again.');
        }
    }

    public function edit($id)
    {
        if((int) $id < 1){
            return redirect()->route('admin_customer_list')->with('error', 'Customer ID is invalid, please recheck your link again');
        }

        // if edit user details itself
        if(Session::get('admin')->id == $id){
            return redirect()->route('admin_profile');
        }

        $data = Customer::find((int) $id);

        if(!$data){
            return redirect()->route('admin_customer_list')->with('error', 'Customer not found, please recheck your link again');
        }

        return view('admin.customer.form', compact('data'));
    }

    public function do_edit($id, Request $request)
    {
        if((int) $id < 1){
            return back()->withInput($request->flashExcept('password'))->with('error', 'Customer ID is invalid, please reload your page before resubmit');
        }

        $validation = [
            'name' => 'required',
            'phone' => 'required|numeric',
            'address' => 'required',
            'status' => 'required|integer'
        ];

        $message    = [
            'required' => ':attribute field is required',
            'min' => ':attribute must be minimal :min characters'
        ];

        $names      = [
            'name' => 'Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'address' => 'Address',
            'status' => 'Status'
        ];

        $this->validate($request, $validation, $message, $names);

        // validating prevent SQL injection
        $name = Helper::validate_input_string($request->name);
        if(!$name){
            return back()->withInput()->with('error', 'Name must be using alphabet only');
        }
        $email = NULL;
        if(isset($request->email)){
            $email = Helper::validate_input_email($request->email);
            if(!$email){
                return back()->withInput()->with('error', 'Email must be using format: username@domain.com');
            }
        }
        $phone = Helper::validate_input_string($request->phone);
        $address = Helper::validate_input_string($request->address);
        if(!$address){
            return back()->withInput()->with('error', 'Address must be using alphanumeric only');
        }

        $data = Customer::find($id);

        if(!$data){
            return back()->withInput($request->flashExcept('password'))->with('error', 'Customer no found, please reload your page before resubmit');
        }
        
        $data->name = $name;
        $data->email = $email;
        $data->phone = $phone;
        $data->address = $address;
        $data->status = $request->input('status');

        if($data->save()){
            return redirect()->route('admin_customer_edit', $id)->with('success', 'Successfully updated customer details');
        }else{
            return back()->withInput()->with('error', 'Oops, failed to update customer details. Please try again.');
        }
    }

    public function delete($id)
    {
        if((int) $id < 1){
            return redirect()->route('admin_customer_list')->with('error', 'Customer ID is invalid, please try again');
        }

        $data = Customer::find($id);

        if(!$data){
            return redirect()->route('admin_customer_list')->with('error', 'Customer not found, please try again');
        }

        $data->isDeleted = 1;

        if($data->save()){
            return redirect()->route('admin_customer_list', $id)->with('success', 'Successfully deleted customer : '.$data->name);
        }else{
            return back()->with('error', 'Oops, failed to delete customer. Please try again.');
        }
    }

    public function list_deleted()
    {
        $deleted = Customer::where('isDeleted', 1)
            ->select('id', 'phone', 'email', 'name', 'status', 'updated_at')
            ->orderBy('name')
            ->paginate(10);

        return view ('admin.customer.list', compact('deleted'));
    }

    public function restore($id)
    {
        if((int) $id < 1){
            return redirect()->route('admin_customer_deleted')->with('error', 'Customer ID is invalid, please try again');
        }

        $data = Customer::find($id);

        if(!$data){
            return redirect()->route('admin_customer_deleted')->with('error', 'Customer not found, please try again');
        }

        $data->isDeleted = 0;

        if($data->save()){
            return redirect()->route('admin_customer_deleted', $id)->with('success', 'Successfully restored customer : '.$data->name);
        }else{
            return back()->with('error', 'Oops, failed to restore customer. Please try again.');
        }
    }
}