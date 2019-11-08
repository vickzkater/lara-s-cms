<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

use App\Models\Branch;
use App\Models\Division;

// library
use App\Libraries\Helper;

class BranchController extends Controller
{
    // set this module
    private $module = 'Branch';

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

        $data = Branch::leftJoin('divisions', 'divisions.id', 'branch.division_id')
            ->where('branch.isDeleted', 0)
            ->select('branch.*', 'divisions.name as division_name')
            ->orderBy('divisions.name')
            ->orderBy('branch.name');

        // get only allowed division
        if (count($allowed_divisions) > 0)
        {
            $data->where(function ($query) use ($allowed_divisions) {
                foreach ($allowed_divisions as $item) {
                    $query->orWhere('divisions.name', '=', $item);
                }
            });
        }

        $data = $data->paginate(10);

        return view ('admin.branch.list', compact('data'));
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

        $divisions = Division::where('isDeleted', 0)
            ->where('status', 1);
        
            // get only allowed division
        if (count($allowed_divisions) > 0)
        {
            $divisions->where(function ($query) use ($allowed_divisions) {
                foreach ($allowed_divisions as $item) {
                    $query->orWhere('name', '=', $item);
                }
            });
        }
        $divisions = $divisions->get();
        return view('admin.branch.form', compact('divisions'));
    }

    public function do_create(Request $request)
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'Add New');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

        $validation = [
            'division_id' => 'required|integer',
            'name' => 'required'
        ];

        $message    = [
            'required' => ':attribute field is required'
        ];

        $names      = [
            'division_id' => 'Division',
            'name' => 'Name'
        ];

        $this->validate($request, $validation, $message, $names);

        // validating prevent SQL injection
        $division_id = (int) $request->division_id;
        if($division_id < 1){
            return back()->withInput()->with('error', 'Division must be choosen at least one');
        }
        $name = Helper::validate_input_string($request->name);
        if(!$name){
            return back()->withInput()->with('error', 'Invalid format for Name');
        }
        $location = Helper::validate_input_string($request->location);
        $phone = Helper::validate_input_string($request->phone);
        if(!empty($phone) && !is_numeric($phone)){
            return back()->withInput()->with('error', 'Invalid format for Phone');
        }
        $status = (int) $request->status;

        $data = new Branch();
        $data->division_id = $division_id;
        $data->name = $name;
        $data->location = $location;
        $data->phone = $phone;
        $data->status = $status;

        if($data->save()){
            return redirect()->route('admin_branch_list')->with('success', 'Successfully added a new branch : '.$name);
        }

        return back()->withInput()->with('error', 'Oops, failed to add a new branch. Please try again.');
    }

    public function edit($id)
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'View Details');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

        if((int) $id < 1){
            return redirect()->route('admin_branch_list')->with('error', 'Branch ID is invalid, please recheck your link again');
        }

        $data = Branch::find($id);

        if(!$data){
            return redirect()->route('admin_branch_list')->with('error', 'Branch not found, please recheck your link again');
        }

        $divisions = Division::where('isDeleted', 0)->where('status', 1)->get();
        return view('admin.branch.form', compact('data', 'divisions'));
    }

    public function do_edit($id, Request $request)
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'Edit');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

        $validation = [
            'division_id' => 'required|integer',
            'name' => 'required'
        ];

        $message    = [
            'required' => ':attribute field is required'
        ];

        $names      = [
            'division_id' => 'Division',
            'name' => 'Name'
        ];

        $this->validate($request, $validation, $message, $names);

        // validating prevent SQL injection
        $division_id = (int) $request->division_id;
        if($division_id < 1){
            return back()->withInput()->with('error', 'Division must be choosen at least one');
        }
        $name = Helper::validate_input_string($request->name);
        if(!$name){
            return back()->withInput()->with('error', 'Invalid format for Name');
        }
        $location = Helper::validate_input_string($request->location);
        $phone = Helper::validate_input_string($request->phone);
        if(!empty($phone) && !is_numeric($phone)){
            return back()->withInput()->with('error', 'Invalid format for Phone');
        }
        $status = (int) $request->status;

        // get existing data
        $data = Branch::find($id);

        if(!$data){
            return back()->withInput()->with('error', 'Branch not found, please reload your page before resubmit');
        }

        $data->division_id = $division_id;
        $data->name = $name;
        $data->location = $location;
        $data->phone = $phone;
        $data->status = $status;

        if($data->save()){
            return redirect()->route('admin_branch_edit', $id)->with('success', 'Successfully updated branch : '.$name);
        }
        return back()->withInput()->with('error', 'Oops, failed to updated branch. Please try again.');
    }

    public function delete($id)
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'Delete');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

        if((int) $id < 1){
            return redirect()->route('admin_branch_list')->with('error', 'Branch ID is invalid, please try again');
        }

        $data = Branch::find($id);

        if(!$data){
            return redirect()->route('admin_branch_list')->with('error', 'Branch not found, please try again');
        }

        $data->isDeleted = 1;

        if($data->save()){
            return redirect()->route('admin_branch_list', $id)->with('success', 'Successfully deleted branch : '.$data->name);
        }else{
            return back()->with('error', 'Oops, failed to delete branch. Please try again.');
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

        $deleted = Branch::leftJoin('divisions', 'divisions.id', 'branch.division_id')
            ->where('branch.isDeleted', 1)
            ->select('branch.*', 'divisions.name as division_name')
            ->orderBy('divisions.name')
            ->orderBy('branch.name');

        // get only allowed division
        if (count($allowed_divisions) > 0)
        {
            $deleted->where(function ($query) use ($allowed_divisions) {
                foreach ($allowed_divisions as $item) {
                    $query->orWhere('divisions.name', '=', $item);
                }
            });
        }

        $deleted = $deleted->paginate(10);

        return view ('admin.branch.list', compact('deleted'));
    }

    public function restore($id)
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'Restore');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

        if((int) $id < 1){
            return redirect()->route('admin_branch_deleted')->with('error', 'Branch ID is invalid, please try again');
        }

        $data = Branch::find($id);

        if(!$data){
            return redirect()->route('admin_branch_deleted')->with('error', 'Branch not found, please try again');
        }

        $data->isDeleted = 0;

        if($data->save()){
            return redirect()->route('admin_branch_deleted', $id)->with('success', 'Successfully restored branch : '.$data->name);
        }else{
            return back()->with('error', 'Oops, failed to restore branch. Please try again.');
        }
    }
}
