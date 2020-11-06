<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductImport;
use App\Exports\ProductExportView;
use App\Exports\ProductTemplateExportView;

// LIBRARIES
use App\Libraries\Helper;

// MODELS
use App\Models\Product;

class ProductController extends Controller
{
    // SET THIS MODULE
    private $module = 'Product';

    // SET THIS OBJECT/ITEM NAME
    private $item = 'product';

    public function list()
    {
        // AUTHORIZING...
        $authorize = Helper::authorizing($this->module, 'View List');
        if ($authorize['status'] != 'true') {
            return back()->with('error', $authorize['message']);
        }

        // FOR DISPLAY ACTIVE DATA
        $data = true;

        return view('admin.product.list', compact('data'));
    }

    public function get_data(Datatables $datatables, Request $request)
    {
        // AUTHORIZING...
        $authorize = Helper::authorizing($this->module, 'View List');
        if ($authorize['status'] != 'true') {
            return back()->with('error', $authorize['message']);
        }

        // SET THIS OBJECT/ITEM NAME BASED ON TRANSLATION
        $this->item = ucwords(lang($this->item, $this->translation));

        // GET THE DATA
        $query = Product::whereNull('replaced_at');

        return $datatables->eloquent($query)
            ->addColumn('item_status', function ($data) {
                if ($data->status != 1) {
                    return '<span class="label label-danger"><i>' . ucwords(lang('disabled', $this->translation)) . '</i></span>';
                }
                return '<span class="label label-success">' . ucwords(lang('enabled', $this->translation)) . '</span>';
            })
            ->addColumn('action', function ($data) {
                $html = '<a href="' . route('admin.product.edit', $data->id) . '" class="btn btn-xs btn-primary" title="' . ucwords(lang('edit', $this->translation)) . '"><i class="fa fa-pencil"></i>&nbsp; ' . ucwords(lang('edit', $this->translation)) . '</a>';

                $html .= '<form action="' . route('admin.product.delete') . '" method="POST" onsubmit="return confirm(\'' . lang('Are you sure to delete this #item?', $this->translation, ['#item' => $this->item]) . '\');" style="display: inline"> ' . csrf_field() . ' <input type="hidden" name="id" value="' . $data->id . '">
                <button type="submit" class="btn btn-xs btn-danger" title="' . ucwords(lang('delete', $this->translation)) . '"><i class="fa fa-trash"></i>&nbsp; ' . ucwords(lang('delete', $this->translation)) . '</button></form>';

                return $html;
            })
            ->addColumn('image_item', function ($data) {
                return '<img src=' . asset($data->image) . ' style="max-width:100px;">';
            })
            ->editColumn('updated_at', function ($data) {
                return Helper::time_ago(strtotime($data->updated_at), lang('ago', $this->translation), Helper::get_periods($this->translation));
            })
            ->editColumn('created_at', function ($data) {
                return $data->created_at;
            })
            ->rawColumns(['item_status', 'action', 'image_item'])
            ->toJson();
    }

    public function create()
    {
        // AUTHORIZING...
        $authorize = Helper::authorizing($this->module, 'Add New');
        if ($authorize['status'] != 'true') {
            return back()->with('error', $authorize['message']);
        }

        return view('admin.product.form');
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
            'title' => 'required',
            'subtitle' => 'required',
            'image' => 'required|image|max:2048',
            'description' => 'required',
            'purchase_date' => 'required|date_format:d/m/Y'
        ];
        // IF UPLOAD FILE
        if ($request->attachments) {
            $validation['attachments'] = 'required|max:2048';
        }
        $message = [
            'required' => ':attribute ' . lang('field is required', $this->translation),
            'image' => ':attribute ' . lang('must be an image', $this->translation)
        ];
        $names = [
            'title' => ucwords(lang('title', $this->translation)),
            'subtitle' => ucwords(lang('subtitle', $this->translation)),
            'image' => ucwords(lang('image', $this->translation)),
            'description' => ucwords(lang('description', $this->translation)),
            'attachments' => ucwords(lang('attachments', $this->translation))
        ];
        $this->validate($request, $validation, $message, $names);

        // INSERT NEW DATA
        $data = new Product();

        // HELPER VALIDATION FOR PREVENT SQL INJECTION & XSS ATTACK
        $title = Helper::validate_input_text($request->title);
        if (!$title) {
            return back()
                ->withInput()
                ->with('error', lang('Invalid format for #item', $this->translation, ['#item' => ucwords(lang('title', $this->translation))]));
        }
        $data->title = $title;

        $subtitle = Helper::validate_input_text($request->subtitle);
        if (!$subtitle) {
            return back()
                ->withInput()
                ->with('error', lang('Invalid format for #item', $this->translation, ['#item' => ucwords(lang('subtitle', $this->translation))]));
        }
        $data->subtitle = $subtitle;

        $description = Helper::validate_input_text($request->description);
        if (!$description) {
            return back()
                ->withInput()
                ->with('error', lang('Invalid format for #item', $this->translation, ['#item' => ucwords(lang('description', $this->translation))]));
        }
        $data->description = $description;

        $data->status = (int) $request->status;

        // PROCESSING IMAGE
        $dir_path = 'uploads/product/';
        $image_file = $request->file('image');
        $format_image_name = time() . '-product';
        $image = Helper::upload_image($dir_path, $image_file, true, $format_image_name);
        if ($image['status'] != 'true') {
            return back()
                ->withInput()
                ->with('error', lang($image['message'], $this->translation, $image['dynamic_objects']));
        }
        // GET THE UPLOADED IMAGE RESULT
        $data->image = $dir_path . $image['data'];

        // IF UPLOAD FILE
        if ($request->attachments) {
            // PROCESSING FILE
            $dir_path = 'uploads/product/';
            $file = $request->file('attachments');
            $format_file_name = time() . '-attachments';
            $file_data = Helper::upload_file($dir_path, $file, true, $format_file_name);
            if ($file_data['status'] != 'true') {
                return back()
                    ->withInput()
                    ->with('error', lang($file_data['message'], $this->translation));
            }
            // GET THE UPLOADED FILE RESULT
            $data->attachments = $dir_path . $file_data['data'];
        }

        // MANIPULATE DATE
        $data->purchase_date = Helper::convert_datepicker($request->purchase_date);
        $expired_date = null;
        if ($request->expired_date) {
            $data->$expired_date = Helper::convert_datepicker($request->expired_date);
        }

        if ((int) $request->qty > 0) {
            // REMOVE COMMA FROM THE DATA
            $data->qty = (int) str_replace(',', '', $request->qty);
        }

        // SAVE THE DATA
        if ($data->save()) {
            // SUCCESS
            return redirect()
                ->route('admin.product.list')
                ->with('success', lang('Successfully added a new #item : #name', $this->translation, ['#item' => $this->item, '#name' => $title]));
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
                ->route('admin.product.list')
                ->with('error', lang('#item ID is invalid, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        // GET THE DATA BASED ON ID
        $data = Product::find($id);

        // CHECK IS DATA FOUND
        if (!$data) {
            // DATA NOT FOUND
            return redirect()
                ->route('admin.product.list')
                ->with('error', lang('#item not found, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        return view('admin.product.form', compact('data'));
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
                ->route('admin.product.list')
                ->with('error', lang('#item ID is invalid, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        // GET THE DATA BASED ON ID
        $data = Product::find($id);

        // CHECK IS DATA FOUND
        if (!$data) {
            // DATA NOT FOUND
            return back()
                ->withInput()
                ->with('error', lang('#item not found, please reload your page before resubmit', $this->translation, ['#item' => $this->item]));
        }

        // LARAVEL VALIDATION
        $validation = [
            'title' => 'required',
            'subtitle' => 'required',
            'description' => 'required',
            'purchase_date' => 'required|date_format:d/m/Y'
        ];
        // IF UPLOAD NEW IMAGE
        if ($request->image) {
            $validation['image'] = 'required|image|max:2048';
        }
        // IF UPLOAD NEW FILE
        if ($request->attachments) {
            $validation['attachments'] = 'required|max:2048';
        }
        $message = [
            'required' => ':attribute ' . lang('field is required', $this->translation),
            'image' => ':attribute ' . lang('must be an image', $this->translation)
        ];
        $names = [
            'title' => ucwords(lang('title', $this->translation)),
            'subtitle' => ucwords(lang('subtitle', $this->translation)),
            'image' => ucwords(lang('image', $this->translation)),
            'description' => ucwords(lang('description', $this->translation)),
            'attachments' => ucwords(lang('attachments', $this->translation))
        ];
        $this->validate($request, $validation, $message, $names);

        // HELPER VALIDATION FOR PREVENT SQL INJECTION & XSS ATTACK
        $title = Helper::validate_input_text($request->title);
        if (!$title) {
            return back()
                ->withInput()
                ->with('error', lang('Invalid format for #item', $this->translation, ['#item' => ucwords(lang('title', $this->translation))]));
        }
        $data->title = $title;

        $subtitle = Helper::validate_input_text($request->subtitle);
        if (!$subtitle) {
            return back()
                ->withInput()
                ->with('error', lang('Invalid format for #item', $this->translation, ['#item' => ucwords(lang('subtitle', $this->translation))]));
        }
        $data->subtitle = $subtitle;

        $description = Helper::validate_input_text($request->description);
        if (!$description) {
            return back()
                ->withInput()
                ->with('error', lang('Invalid format for #item', $this->translation, ['#item' => ucwords(lang('description', $this->translation))]));
        }
        $data->description = $description;

        $data->status = (int) $request->status;

        // IF UPLOAD NEW IMAGE
        if ($request->image) {
            // PROCESSING IMAGE
            $dir_path = 'uploads/product/';
            $image_file = $request->file('image');
            $format_image_name = time() . '-product';
            $image = Helper::upload_image($dir_path, $image_file, true, $format_image_name);
            if ($image['status'] != 'true') {
                return back()
                    ->withInput()
                    ->with('error', lang($image['message'], $this->translation, $image['dynamic_objects']));
            }
            // GET THE UPLOADED IMAGE RESULT
            $data->image = $dir_path . $image['data'];
        } elseif (isset($request->image_delete) && $request->image_delete == 'yes') {
            // DELETE EXISTING IMAGE WITHOUT UPLOAD THE NEW ONE
            // $image = 'images/no-image.png';
            $data->image = null;
        }

        // IF UPLOAD NEW FILE
        if ($request->attachments) {
            // PROCESSING FILE
            $dir_path = 'uploads/product/';
            $file = $request->file('attachments');
            $format_file_name = time() . '-attachments';
            $file_data = Helper::upload_file($dir_path, $file, true, $format_file_name);
            if ($file_data['status'] != 'true') {
                return back()
                    ->withInput()
                    ->with('error', lang($file_data['message'], $this->translation));
            }
            // GET THE UPLOADED FILE RESULT
            $data->attachments = $dir_path . $file_data['data'];
        } elseif (isset($request->attachments_delete) && $request->attachments_delete == 'yes') {
            // DELETE EXISTING FILE WITHOUT UPLOAD THE NEW ONE
            $data->attachments = null;
        }

        // MANIPULATE DATE
        $data->purchase_date = Helper::convert_datepicker($request->purchase_date);
        $expired_date = null;
        if ($request->expired_date) {
            $data->$expired_date = Helper::convert_datepicker($request->expired_date);
        }

        if ((int) $request->qty > 0) {
            // REMOVE COMMA FROM THE DATA
            $data->qty = (int) str_replace(',', '', $request->qty);
        }

        // UPDATE THE DATA
        if ($data->save()) {
            // SUCCESS
            return redirect()
                ->route('admin.product.edit', $id)
                ->with('success', lang('Successfully updated #item : #name', $this->translation, ['#item' => $this->item, '#name' => $title]));
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
                ->route('admin.product.list')
                ->with('error', lang('#item ID is invalid, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        // GET THE DATA BASED ON ID
        $data = Product::find($id);

        // CHECK IS DATA FOUND
        if (!$data) {
            // DATA NOT FOUND
            return redirect()
                ->route('admin.product.list')
                ->with('error', lang('#item not found, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        // DELETE THE DATA
        if ($data->delete()) {
            // SUCCESS
            return redirect()
                ->route('admin.product.list')
                ->with('success', lang('Successfully deleted #item : #name', $this->translation, ['#item' => $this->item, '#name' => $data->title]));
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

        return view('admin.product.list');
    }

    public function get_data_deleted(Datatables $datatables, Request $request)
    {
        // AUTHORIZING...
        $authorize = Helper::authorizing($this->module, 'View List');
        if ($authorize['status'] != 'true') {
            return back()->with('error', $authorize['message']);
        }

        // SET THIS OBJECT/ITEM NAME BASED ON TRANSLATION
        $this->item = ucwords(lang($this->item, $this->translation));

        // GET THE DATA
        $query = Product::onlyTrashed()->whereNotNull('id');

        return $datatables->eloquent($query)
            ->addColumn('item_status', function ($data) {
                if ($data->status != 1) {
                    return '<span class="label label-danger"><i>' . ucwords(lang('disabled', $this->translation)) . '</i></span>';
                }
                return '<span class="label label-success">' . ucwords(lang('enabled', $this->translation)) . '</span>';
            })
            ->addColumn('action', function ($data) {
                return '<form action="' . route('admin.product.restore') . '" method="POST" onsubmit="return confirm(\'' . lang('Are you sure to restore this #item?', $this->translation, ['#item' => $this->item]) . '\');" style="display: inline"> ' . csrf_field() . ' <input type="hidden" name="id" value="' . $data->id . '">
                <button type="submit" class="btn btn-xs btn-primary" title="' . ucwords(lang('restore', $this->translation)) . '"><i class="fa fa-check"></i>&nbsp; ' . ucwords(lang('restore', $this->translation)) . '</button></form>';
            })
            ->addColumn('image_item', function ($data) {
                return '<img src=' . asset($data->image) . ' style="max-width:100px;">';
            })
            ->editColumn('deleted_at', function ($data) {
                return Helper::time_ago(strtotime($data->deleted_at), lang('ago', $this->translation), Helper::get_periods($this->translation));
            })
            ->editColumn('created_at', function ($data) {
                return $data->created_at;
            })
            ->rawColumns(['item_status', 'action', 'image_item'])
            ->toJson();
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
                ->route('admin.product.deleted')
                ->with('error', lang('#item ID is invalid, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        // GET THE DATA BASED ON ID
        $data = Product::onlyTrashed()->find($id);

        // CHECK IS DATA FOUND
        if (!$data) {
            // DATA NOT FOUND
            return redirect()
                ->route('admin.product.deleted')
                ->with('error', lang('#item not found, please recheck your link again', $this->translation, ['#item' => $this->item]));
        }

        // RESTORE THE DATA
        if ($data->restore()) {
            // SUCCESS
            return redirect()
                ->route('admin.product.deleted')
                ->with('success', lang('Successfully restored #item : #name', $this->translation, ['#item' => $this->item, '#name' => $data->title]));
        }

        // FAILED
        return back()
            ->with('error', lang('Oops, failed to restore #item. Please try again.', $this->translation, ['#item' => $this->item]));
    }

    public function import_excel(Request $request)
    {
        // AUTHORIZING...
        $authorize = Helper::authorizing($this->module, 'Import Data');
        if ($authorize['status'] != 'true') {
            return back()->with('error', $authorize['message']);
        }

        // SET THIS OBJECT/ITEM NAME BASED ON TRANSLATION
        $this->item = ucwords(lang($this->item, $this->translation));

        // LARAVEL VALIDATION
        $this->validate($request, [
            'file' => 'required|mimes:csv,xls,xlsx'
        ]);

        // GET THE UPLOADED FILE
        $file = $request->file('file');

        // RENAME THE FILE
        $nama_file = date('YmdHis') . '_' . $file->getClientOriginalName();

        // CONFIG
        $dir_path = 'uploads/excel/product';
        $destination_path = public_path($dir_path);

        // UPLOAD TO THE DESTINATION PATH ($dir_path) IN PUBLIC FOLDER
        if ($file->move($destination_path, $nama_file)) {
            // SET FLAG FOR OLD DATA
            Product::whereNull('replaced_at')
                ->update(['replaced_at' => date('Y-m-d H:i:s')]);

            // IMPORT DATA
            Excel::import(new ProductImport, public_path($dir_path . '/' . $nama_file));

            // SUCCESS
            return redirect()
                ->route('admin.product.list')
                ->with('success', lang('Successfully imported data #item', $this->translation, ['#item' => $this->item]));
        }

        // FAILED
        return back()
            ->withInput()
            ->with('error', lang('Oops, failed to imported data #item. Please try again.', $this->translation, ['#item' => $this->item]));
    }

    public function export_excel()
    {
        // AUTHORIZING...
        $authorize = Helper::authorizing($this->module, 'Export Excel');
        if ($authorize['status'] != 'true') {
            return back()->with('error', $authorize['message']);
        }

        // SET FILE NAME
        $filename = date('YmdHis') . '_larascms_products';

        return Excel::download(new ProductExportView, $filename . '.xlsx');
    }

    public function import_excel_template()
    {
        // AUTHORIZING...
        $authorize = Helper::authorizing($this->module, 'Import Excel');
        if ($authorize['status'] != 'true') {
            return back()->with('error', $authorize['message']);
        }

        // SET FILE NAME
        $filename = 'larascms_products_import_template';

        return Excel::download(new ProductTemplateExportView, $filename . '.xlsx');
    }
}
