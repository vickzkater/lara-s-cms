<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

// library
use App\Libraries\Helper;

// Models
use App\Models\Topic;

class TopicController extends Controller
{
    // SET THIS MODULE
    private $module = 'Topic';

    // SET THIS OBJECT/ITEM NAME
    private $item = 'topic';

    public function list()
    {
        // AUTHORIZING...
        $authorize = Helper::authorizing($this->module, 'View List');
        if ($authorize['status'] != 'true') {
            return back()->with('error', $authorize['message']);
        }

        // FOR DISPLAY ACTIVE DATA
        $data = true;

        return view('admin.topic.list', compact('data'));
    }

    public function get_data(Datatables $datatables, Request $request)
    {
        // AUTHORIZING...
        $authorize = Helper::authorizing($this->module, 'View List');
        if ($authorize['status'] != 'true') {
            return response()->json([
                'status' => 'false',
                'message' => $authorize['message']
            ]);
        }

        // SET THIS OBJECT/ITEM NAME BASED ON TRANSLATION
        $this->item = ucwords(lang($this->item, $this->translation));

        // GET THE DATA
        $query = Topic::whereNotNull('id');

        return $datatables->eloquent($query)
            ->addColumn('item_status', function ($data) {
                if ($data->status != 1) {
                    return '<span class="label label-danger"><i>' . ucwords(lang('disabled', $this->translation)) . '</i></span>';
                }
                return '<span class="label label-success">' . ucwords(lang('enabled', $this->translation)) . '</span>';
            })
            ->addColumn('action', function ($data) {
                $html = '<a href="' . route('admin.topic.edit', $data->id) . '" class="btn btn-xs btn-primary" title="' . ucwords(lang('edit', $this->translation)) . '"><i class="fa fa-pencil"></i>&nbsp; ' . ucwords(lang('edit', $this->translation)) . '</a>';

                $html .= '<form action="' . route('admin.topic.delete') . '" method="POST" onsubmit="return confirm(\'' . lang('Are you sure to delete this #item?', $this->translation, ['#item' => $this->item]) . '\');" style="display: inline"> ' . csrf_field() . ' <input type="hidden" name="id" value="' . $data->id . '">
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

        return view('admin.topic.form');
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
            'name' => 'required|unique:topics,name'
        ];
        $message = [
            'required' => ':attribute ' . lang('field is required', $this->translation),
            'unique' => ':attribute ' . lang('has been used, please input another data', $this->translation)
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

        // SAVE THE DATA
        $data = new Topic();
        $data->name = $name;
        $data->status = (int) $request->status;

        if ($data->save()) {
            // SUCCESS
            return redirect()
                ->route('admin.topic.list')
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

        // SET THIS OBJECT/ITEM NAME BASED ON TRANSLATION
        $this->item = ucwords(lang($this->item, $this->translation));

        // CHECK OBJECT ID
        if ((int) $id < 1) {
            // INVALID OBJECT ID
            return redirect()
                ->route('admin.topic.list')
                ->with('error', lang('#item ID is invalid, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        // GET THE DATA BASED ON ID
        $data = Topic::find($id);

        // CHECK IS DATA FOUND
        if (!$data) {
            // DATA NOT FOUND
            return redirect()
                ->route('admin.topic.list')
                ->with('error', lang('#item not found, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        return view('admin.topic.form', compact('data'));
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
                ->route('admin.topic.list')
                ->with('error', lang('#item ID is invalid, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

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
        $data = Topic::find($id);

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
            // SUCCESS
            return redirect()
                ->route('admin.topic.edit', $id)
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
                ->route('admin.topic.list')
                ->with('error', lang('#item ID is invalid, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        // GET THE DATA BASED ON ID
        $data = Topic::find($id);

        // CHECK IS DATA FOUND
        if (!$data) {
            // DATA NOT FOUND
            return redirect()
                ->route('admin.topic.list')
                ->with('error', lang('#item not found, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        // DELETE THE DATA
        if ($data->delete()) {
            // SUCCESS
            return redirect()
                ->route('admin.topic.list')
                ->with('success', lang('Successfully deleted #item : #name', $this->translation, ['#item' => $this->item, '#name' => $data->name]));
        }

        // FAILED
        return back()
            ->with('error', lang('Oops, failed to delete #item. Please try again.', $this->translation, ['#item' => $this->item]));
    }
}
