<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

// LIBRARIES
use App\Libraries\Helper;

// MODELS
use App\Models\Banner;

class BannerController extends Controller
{
    // SET THIS MODULE
    private $module = 'Banner';
    // SET THIS OBJECT/ITEM NAME
    private $item = 'banner';

    public function list()
    {
        // AUTHORIZING...
        $authorize = Helper::authorizing($this->module, 'View List');
        if ($authorize['status'] != 'true') {
            return back()->with('error', $authorize['message']);
        }

        // FOR DISPLAY ACTIVE DATA
        $data = true;

        return view('admin.banner.list', compact('data'));
    }

    public function get_data(Request $request)
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

        // AJAX OR API VALIDATOR
        $validation_rules = [
            // 'division' => 'required'
        ];
        $validator = Validator::make($request->all(), $validation_rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'false',
                'message' => 'Validation Error',
                'data' => $validator->errors()->messages()
            ]);
        }

        // GET THE DATA
        $query = Banner::whereNotNull('id');

        // PROVIDE THE DATA
        $data = $query->orderBy('ordinal')->get();

        // MANIPULATE THE DATA
        if (!empty($data)) {
            foreach ($data as $item) {
                $item->created_at_edited = date('Y-m-d H:i:s');
                $item->updated_at_edited = Helper::time_ago(strtotime($item->updated_at), lang('ago', $this->translation), Helper::get_periods($this->translation));
                $item->deleted_at_edited = Helper::time_ago(strtotime($item->deleted_at), lang('ago', $this->translation), Helper::get_periods($this->translation));
                $item->image_item = asset($item->image);
            }
        }

        // SUCCESS
        $response = [
            'status' => 'true',
            'message' => 'Successfully get data list',
            'data' => $data
        ];
        return response()->json($response, 200);
    }

    public function create()
    {
        // AUTHORIZING...
        $authorize = Helper::authorizing($this->module, 'Add New');
        if ($authorize['status'] != 'true') {
            return back()->with('error', $authorize['message']);
        }

        return view('admin.banner.form');
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

        // INSERT NEW DATA
        $data = new Banner();

        // LARAVEL VALIDATION
        $validation = [
            'image' => 'required|image'
        ];
        $message = [
            'required' => ':attribute ' . lang('field is required', $this->translation)
        ];
        $names = [
            'image' => ucwords(lang('image', $this->translation))
        ];
        $this->validate($request, $validation, $message, $names);

        // HELPER VALIDATION FOR PREVENT SQL INJECTION & XSS ATTACK
        // PROCESSING IMAGE
        $dir_path = 'uploads/banner/';
        $image_file = $request->file('image');
        $format_image_name = time() . '-banner';
        $image = Helper::upload_image($dir_path, $image_file, true, $format_image_name);
        if ($image['status'] != 'true') {
            return back()
                ->withInput()
                ->with('error', lang($image['message'], $this->translation, $image['dynamic_objects']));
        }
        // GET THE UPLOADED IMAGE RESULT
        $data->image = $dir_path . $image['data'];

        $data->title = Helper::validate_input_text($request->title);
        $data->description = Helper::validate_input_text($request->description);
        $data->status = (int) $request->status;

        // SET ORDER / ORDINAL
        $last = Banner::select('ordinal')->orderBy('ordinal', 'desc')->first();
        $ordinal = 1;
        if ($last) {
            $ordinal = $last->ordinal + 1;
        }
        $data->ordinal = $ordinal;

        // SAVE THE DATA
        if ($data->save()) {
            // SUCCESS
            return redirect()
                ->route('admin.banner.list')
                ->with('success', lang('Successfully added a new #item', $this->translation, ['#item' => $this->item]));
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
                ->route('admin.banner.list')
                ->with('error', lang('#item ID is invalid, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        // GET THE DATA BASED ON ID
        $data = Banner::find($id);

        // CHECK IS DATA FOUND
        if (!$data) {
            // DATA NOT FOUND
            return redirect()
                ->route('admin.banner.list')
                ->with('error', lang('#item not found, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        return view('admin.banner.form', compact('data'));
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
                ->route('admin.banner.list')
                ->with('error', lang('#item ID is invalid, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        // GET THE DATA BASED ON ID
        $data = Banner::find($id);

        // CHECK IS DATA FOUND
        if (!$data) {
            // DATA NOT FOUND
            return back()
                ->withInput()
                ->with('error', lang('#item not found, please reload your page before resubmit', $this->translation, ['#item' => $this->item]));
        }

        // LARAVEL VALIDATION
        if ($request->image) {
            $validation = [
                'image' => 'required|image'
            ];
            $message = [
                'required' => ':attribute ' . lang('field is required', $this->translation)
            ];
            $names = [
                'image' => ucwords(lang('image', $this->translation))
            ];
            $this->validate($request, $validation, $message, $names);
        }

        // HELPER VALIDATION FOR PREVENT SQL INJECTION & XSS ATTACK
        // IF UPLOAD NEW IMAGE
        if ($request->image) {
            // PROCESSING IMAGE
            $dir_path = 'uploads/banner/';
            $image_file = $request->file('image');
            $format_image_name = time() . '-banner';
            $image = Helper::upload_image($dir_path, $image_file, true, $format_image_name);
            if ($image['status'] != 'true') {
                return back()
                    ->withInput()
                    ->with('error', lang($image['message'], $this->translation, $image['dynamic_objects']));
            }
            // GET THE UPLOADED IMAGE RESULT
            $data->image = $dir_path . $image['data'];
        }

        $data->title = Helper::validate_input_text($request->title);
        $data->description = Helper::validate_input_text($request->description);
        $data->status = (int) $request->status;

        // UPDATE THE DATA
        if ($data->save()) {
            // SUCCESS
            return redirect()
                ->route('admin.banner.edit', $id)
                ->with('success', lang('Successfully updated #item', $this->translation, ['#item' => $this->item]));
        }

        // FAILED
        return back()
            ->withInput()
            ->with('error', lang('Oops, failed to update #item. Please try again.', $this->translation, ['#item' => $this->item]));
    }

    public function sorting(Request $request)
    {
        // AJAX OR API VALIDATOR
        $validation_rules = [
            'rows' => 'required'
        ];

        $validator = Validator::make($request->all(), $validation_rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'false',
                'message' => 'Validation Error',
                'data' => $validator->errors()->messages()
            ]);
        }

        // JSON Array - sample: row[]=2&row[]=1&row[]=3
        $rows = $request->input('rows');

        // convert to array
        $data = explode('&', $rows);

        $ordinal = 1;
        foreach ($data as $item) {
            // split the data
            $tmp = explode('[]=', $item);

            $object = Banner::find($tmp[1]);
            $object->ordinal = $ordinal;
            $object->save();

            $ordinal++;
        }

        // SUCCESS
        $response = [
            'status' => 'true',
            'message' => 'Successfully rearranged data',
            'data' => $data
        ];
        return response()->json($response, 200);
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
                ->route('admin.banner.list')
                ->with('error', lang('#item ID is invalid, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        // GET THE DATA BASED ON ID
        $data = Banner::find($id);

        // CHECK IS DATA FOUND
        if (!$data) {
            // DATA NOT FOUND
            return redirect()
                ->route('admin.banner.list')
                ->with('error', lang('#item not found, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        // DELETE THE DATA
        if ($data->delete()) {
            // SUCCESS
            return redirect()
                ->route('admin.banner.list')
                ->with('success', lang('Successfully deleted #item', $this->translation, ['#item' => $this->item]));
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

        return view('admin.banner.list');
    }

    public function get_data_deleted(Request $request)
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

        // AJAX OR API VALIDATOR
        $validation_rules = [
            // 'division' => 'required'
        ];
        $validator = Validator::make($request->all(), $validation_rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'false',
                'message' => 'Validation Error',
                'data' => $validator->errors()->messages()
            ]);
        }

        // GET THE DATA
        $query = Banner::onlyTrashed();

        // PROVIDE THE DATA
        $data = $query->orderBy('ordinal')->get();

        // MANIPULATE THE DATA
        if (!empty($data)) {
            foreach ($data as $item) {
                $item->created_at_edited = date('Y-m-d H:i:s');
                $item->updated_at_edited = Helper::time_ago(strtotime($item->updated_at), lang('ago', $this->translation), Helper::get_periods($this->translation));
                $item->deleted_at_edited = Helper::time_ago(strtotime($item->deleted_at), lang('ago', $this->translation), Helper::get_periods($this->translation));
                $item->image_item = asset($item->image);
            }
        }

        // SUCCESS
        $response = [
            'status' => 'true',
            'message' => 'Successfully get data list',
            'data' => $data
        ];
        return response()->json($response, 200);
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

        $id = $request->id;

        // CHECK OBJECT ID
        if ((int) $id < 1) {
            // INVALID OBJECT ID
            return redirect()
                ->route('admin.banner.deleted')
                ->with('error', lang('#item ID is invalid, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        // GET THE DATA BASED ON ID
        $data = Banner::onlyTrashed()->find($id);

        // CHECK IS DATA FOUND
        if (!$data) {
            // DATA NOT FOUND
            return redirect()
                ->route('admin.banner.deleted')
                ->with('error', lang('#item not found, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        // RESTORE THE DATA
        if ($data->restore()) {
            // SUCCESS
            return redirect()
                ->route('admin.banner.deleted')
                ->with('success', lang('Successfully restored #item', $this->translation, ['#item' => $this->item]));
        }

        // FAILED
        return back()
            ->with('error', lang('Oops, failed to restore #item. Please try again.', $this->translation, ['#item' => $this->item]));
    }
}
