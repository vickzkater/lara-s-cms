<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;

// library
use App\Libraries\Helper;

class BannerController extends Controller
{
    public function list()
    {
        $data = Banner::where('isDeleted', 0)
            ->select('id', 'name', 'image', 'text_big', 'text_small', 'order', 'status', 'updated_at')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view ('admin.banner.list', compact('data'));
    }

    public function create()
    {
       return view('admin.banner.form');
    }

    public function do_create(Request $request)
    {
        $validation = [
            'name' => 'required',
            // 'image' => 'required|image|mimes:jpeg,png,jpg|dimensions:max_width=1200,max_height=675|max:2048',
            'image' => 'required|image|mimes:jpeg,png,jpg',
            'link' => 'required',
            'text_big' => 'required',
            'text_small' => 'required',
            'model' => 'required',
            'status' => 'required'
        ];

        $message    = [
            'required' => ':attribute field is required',
            'dimensions' => 'Dimensions image is max(1200x675) dan min(500x500)',
            'max' => 'Max image size is 2 MB'
        ];

        $names      = [
            'name' => 'Name',
            'image' => 'Image',
            'link' => 'Link',
            'text_big' => 'Text BIG',
            'text_small' => 'Text small',
            'model' => 'Model',
            'status' => 'Status'
        ];

        $this->validate($request, $validation, $message, $names);

        // validating prevent SQL injection
        $name = Helper::validate_input_string($request->name);
        if(!$name){
            return back()->withInput()->with('error', 'Invalid format for Name');
        }
        $link = Helper::validate_input($request->link, true);
        if(!$link){
            return back()->withInput()->with('error', 'Invalid format for Link');
        }
        $text_big = Helper::validate_input_string($request->text_big);
        if(!$text_big){
            return back()->withInput()->with('error', 'Invalid format for Text BIG');
        }
        $text_small = Helper::validate_input_string($request->text_small);
        if(!$text_small){
            return back()->withInput()->with('error', 'Invalid format for Text small');
        }
        $model = (int) $request->model;
        if(!in_array($model, [1,2,3])){
            return back()->withInput()->with('error', 'Invalid Model, please choose one');
        }
        $status = (int) $request->status;

        if($request->hasFile('image')){
            $image = $request->file('image');
            $destinationPath = public_path('uploads/banner/');
            $extension  = strtolower($image->getClientOriginalExtension());
            $image_name   = time() . '.' . $extension;
            
            // uploading...
            if ($image->move($destinationPath, $image_name)){
                // get last order
                $last = Banner::select('order')->orderBy('order', 'desc')->first();
                $order = 1;
                if($last){
                    $order = $last->order + 1;
                }
                
                $data = new Banner();
                $data->name = $name;
                $data->image = $image_name;
                $data->link = $link;
                $data->text_big = $text_big;
                $data->text_small = $text_small;
                $data->model = $model;
                $data->status = $status;
                $data->order = $order;
    
                if($data->save()){
                    return redirect()->route('admin_banner_list')->with('success', 'Successfully added a new banner : '.$name);
                }

                // remove uploaded image if failed to save data
                unlink($destinationPath.$image_name);
                return back()->withInput()->with('error', 'Oops, failed to add a new banner. Please try again.');
            }
            return back()->withInput()->with('error', 'Oops, failed to upload new banner. Please try again.');
        }

        return back()->withInput()->with('error', 'Image banner is required. Please try again.');
    }

    public function edit($id)
    {
        if((int) $id < 1){
            return redirect()->route('admin_banner_list')->with('error', 'Banner ID is invalid, please recheck your link again');
        }

        $data = Banner::find($id);

        if(!$data){
            return redirect()->route('admin_banner_list')->with('error', 'Banner not found, please recheck your link again');
        }

        return view('admin.banner.form', compact('data'));
    }

    public function do_edit($id, Request $request)
    {
        $validation = [
            'name' => 'required',
            'link' => 'required',
            'text_big' => 'required',
            'text_small' => 'required',
            'model' => 'required',
            'status' => 'required'
        ];

        // if upload new image
        if($request->hasFile('image')){
            $validation['image'] = 'required|image|mimes:jpeg,png,jpg|dimensions:max_width=1200,max_height=675|max:2048';
        }

        $message    = [
            'required' => ':attribute field is required',
            'dimensions' => 'Dimensions image is max(1200x675) dan min(500x500)',
            'max' => 'Max image size is 2 MB'
        ];

        $names      = [
            'name' => 'Name',
            'image' => 'Image',
            'link' => 'Link',
            'text_big' => 'Text BIG',
            'text_small' => 'Text small',
            'model' => 'Model',
            'status' => 'Status'
        ];

        $this->validate($request, $validation, $message, $names);

        // validating prevent SQL injection
        $name = Helper::validate_input_string($request->name);
        if(!$name){
            return back()->withInput()->with('error', 'Invalid format for Name');
        }
        $link = Helper::validate_input($request->link, true);
        if(!$link){
            return back()->withInput()->with('error', 'Invalid format for Link');
        }
        $text_big = Helper::validate_input_string($request->text_big);
        if(!$text_big){
            return back()->withInput()->with('error', 'Invalid format for Text BIG');
        }
        $text_small = Helper::validate_input_string($request->text_small);
        if(!$text_small){
            return back()->withInput()->with('error', 'Invalid format for Text small');
        }
        $model = (int) $request->model;
        if(!in_array($model, [1,2,3])){
            return back()->withInput()->with('error', 'Invalid Model, please choose one');
        }
        $status = (int) $request->status;

        // get existing data
        $data = Banner::find($id);

        if(!$data){
            return back()->withInput()->with('error', 'Banner not found, please reload your page before resubmit');
        }

        $data->name = $name;
        $data->link = $link;
        $data->text_big = $text_big;
        $data->text_small = $text_small;
        $data->model = $model;
        $data->status = $status;

        // if upload new image
        if($request->hasFile('image')){
            $image = $request->file('image');
            $destinationPath = public_path('uploads/banner/');
            $extension  = strtolower($image->getClientOriginalExtension());
            $image_name   = time() . '.' . $extension;
            
            // uploading...
            if ($image->move($destinationPath, $image_name)){
                // get old image data
                $old_img = $data->image;

                // set data for updating
                $data->image = $image_name;
            }else{
                return back()->withInput()->with('error', 'Oops, failed to upload new banner. Please try again.');
            }
        }

        if($data->save()){
            if($request->hasFile('image')){
                // remove old image
                unlink($destinationPath.$old_img);
            }

            return redirect()->route('admin_banner_edit', $id)->with('success', 'Successfully updated banner : '.$name);
        }

        if($request->hasFile('image')){
            // remove uploaded image if failed to save data
            unlink($destinationPath.$image_name);
        }
        return back()->withInput()->with('error', 'Oops, failed to updated banner. Please try again.');
    }

    public function delete($id)
    {
        if((int) $id < 1){
            return redirect()->route('admin_banner_list')->with('error', 'Banner ID is invalid, please try again');
        }

        $data = Banner::find($id);

        if(!$data){
            return redirect()->route('admin_banner_list')->with('error', 'Banner not found, please try again');
        }

        $data->isDeleted = 1;

        if($data->save()){
            return redirect()->route('admin_banner_list', $id)->with('success', 'Successfully deleted banner : '.$data->name);
        }else{
            return back()->with('error', 'Oops, failed to delete banner. Please try again.');
        }
    }

    public function list_deleted()
    {
        $deleted = Banner::where('isDeleted', 1)
            ->select('id', 'name', 'image', 'text_big', 'text_small', 'order', 'status', 'updated_at')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view ('admin.banner.list', compact('deleted'));
    }

    public function restore($id)
    {
        if((int) $id < 1){
            return redirect()->route('admin_banner_deleted')->with('error', 'Banner ID is invalid, please try again');
        }

        $data = Banner::find($id);

        if(!$data){
            return redirect()->route('admin_banner_deleted')->with('error', 'Banner not found, please try again');
        }

        $data->isDeleted = 0;

        if($data->save()){
            return redirect()->route('admin_banner_deleted', $id)->with('success', 'Successfully restored banner : '.$data->name);
        }else{
            return back()->with('error', 'Oops, failed to restore banner. Please try again.');
        }
    }
}
