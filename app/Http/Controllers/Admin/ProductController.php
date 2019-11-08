<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

// library
use App\Libraries\Helper;

use App\Models\Product;
use App\Models\ProductDetails;
use App\Models\Brand;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Transaction;

class ProductController extends Controller
{
    // set this module
    private $module = 'Product';

    public function list(Request $request)
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'View List');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

        $data = Product::leftJoin('product_details AS dtl', 'dtl.prod_id', 'products.id')
            ->leftJoin('branch', 'branch.id', 'dtl.branch_id')
            ->leftJoin('divisions', 'divisions.id', 'branch.division_id')
            ->where('products.isDeleted', 0)
            ->select('products.id', 'products.name', 'products.currency', 'products.stock', 'products.updated_at', 'products.status', 'products.images', 'products.image_primary', 'dtl.purchase_date', 'products.price_now', 'dtl.qc_status', 'dtl.photo_status', 'dtl.publish_status', 'dtl.booked_date', 'dtl.sold_date', 'branch.name as branch_name', 'divisions.name as division')
            ->orderBy('products.updated_at', 'desc');

        if(isset($request->search) && !empty($request->search)){
            $search = $request->search;
            $data->where(function ($query) use ($search) {
                $query->where('products.name', 'LIKE', '%'.$search.'%');
            });
        }
        
        $data = $data->paginate(10);

        return view ('admin.product.list', compact('data'));
    }

    public function create()
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'Add New');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

        $brands = Brand::where('status', 1)->where('isDeleted', 0)->get();
        $branches = Branch::where('status', 1)->where('isDeleted', 0)->get();

        return view('admin.product.form', compact('brands', 'branches'));
    }

    public function do_create(Request $request)
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'Add New');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

        $validation = [
            'seller_name' => 'required',
            'seller_phone' => 'required',
            'seller_idcard' => 'image|mimes:jpeg,png,jpg',
            'unit_in_tkp' => 'required|image|mimes:jpeg,png,jpg',
            'branch_id' => 'required|integer|min:1',
            'name' => 'required',
            'purchase_date' => 'required',
            'purchase_price' => 'required',
            'brand_id' => 'required|integer|min:1',
            'km' => 'required',
            'tax' => 'required',
            'qc_status' => 'required',
            'modif_status' => 'required'
        ];

        $message    = [
            'required' => ':attribute field is required'
        ];

        $names      = [
            'seller_name' => 'Seller Name',
            'seller_phone' => 'Seller Phone',
            'seller_idcard' => 'KTP',
            'unit_in_tkp' => 'Unit in TKP',
            'branch_id' => 'Branch',
            'name' => 'Name',
            'purchase_date' => 'Purchase Date',
            'purchase_price' => 'Purchase Price',
            'price_now' => 'Sell Price',
            'brand_id' => 'Brand',
            'km' => 'Kilometer',
            'tax' => 'Tax',
            'stock' => 'Stock',
            'qc_status' => 'QC List',
            'modif_status' => 'Modif'
        ];

        $this->validate($request, $validation, $message, $names);

        // sanitizing...
        $seller_name = Helper::validate_input_string($request->seller_name);
        if(!$seller_name){
            return back()->withInput()->with('error', 'Seller Name is required');
        }
        $seller_phone = Helper::validate_input_string($request->seller_phone);
        if(!$seller_phone){
            return back()->withInput()->with('error', 'Seller Phone is required');
        }
        $seller_bank_name = Helper::validate_input_string($request->seller_bank_name);
        $seller_bank_account = Helper::validate_input_string($request->seller_bank_account);
        $branch_id = (int) $request->branch_id;
        if($branch_id < 0){
            return back()->withInput()->with('error', "Branch is required, please choose one");
        }
        $name = Helper::validate_input_string($request->name);
        if(!$name){
            return back()->withInput()->with('error', 'Name is required');
        }
        $purchase_date = Helper::validate_input_string($request->purchase_date);
        $purchase_price = str_replace(',', '', Helper::validate_input_string($request->purchase_price));
        $price_now = str_replace(',', '', Helper::validate_input_string($request->price_now));
        if(empty($price_now)){
            $price_now = 0;
        }
        $brand_id = (int) $request->brand_id;
        if($brand_id < 0){
            return back()->withInput()->with('error', "Brand is required, please choose one");
        }
        $km = Helper::validate_input_string($request->km);
        $tax = Helper::validate_input_string($request->tax);
        $stock = 1;
        if($stock < 0){
            return back()->withInput()->with('error', "You can't input minus value for Stock");
        }
        $plat_no = strtoupper(Helper::validate_input_string($request->plat_no));

        // QC List
        $qc_status = Helper::validate_input_string($request->qc_status);
        if($qc_status == 'yes_qc'){
            // perlu QC
            $qc_status = 0;

            if(count($request->qc_img) < 1){
                return back()->withInput()->with('error', 'QC item image is required, if it needs to do QC later');
            }
            if(count($request->qc_desc) < 1){
                return back()->withInput()->with('error', 'QC item description is required, if it needs to do QC later');
            }
        }else{
            // lulus QC
            $qc_status = 1;
        }

        // Modif
        $modif_status = Helper::validate_input_string($request->modif_status);
        if($modif_status == 'yes_modif'){
            // ada modif
            $modif_status = 0;

            if(count($request->modif_img) < 1){
                return back()->withInput()->with('error', 'Modif item image is required, if there is modif');
            }
            if(count($request->modif_desc) < 1){
                return back()->withInput()->with('error', 'Modif item description is required, if there is modif');
            }
        }else{
            // tidak ada modif
            $modif_status = 1;
        }
        
        // insert data into table `products`
        $data = new Product();
        $data->name = $name;
        $data->brand_id = $brand_id;
        $data->stock = $stock;
        $data->price_now = $price_now;

        if($data->save()){
            // convert date
            $purchase_date_arr = explode('/', $purchase_date);
            $purchase_date = $purchase_date_arr[2].'-'.$purchase_date_arr[1].'-'.$purchase_date_arr[0];
            $tax_arr = explode('/', $tax);
            $tax = $tax_arr[2].'-'.$tax_arr[1].'-'.$tax_arr[0];

            // insert data into table `product_details`
            $data_details = new ProductDetails();
            $data_details->prod_id = $data->id;
            $data_details->branch_id = $branch_id;
            $data_details->purchase_date = $purchase_date;
            $data_details->purchase_price = $purchase_price;
            $data_details->km = $km;
            $data_details->tax = $tax;
            
            $data_details->seller_name = $seller_name;
            $data_details->seller_phone = $seller_phone;
            $data_details->seller_bank_name = $seller_bank_name;
            $data_details->seller_bank_account = $seller_bank_account;
            $data_details->plat_no = $plat_no;

            // seller_idcard
            $destinationPath = public_path('uploads/product/seller/');
            if($request->hasFile('seller_idcard')){
                $image = $request->file('seller_idcard');
                $extension  = strtolower($image->getClientOriginalExtension());
                // format image name : XXXX-idcard-[product_id].ext
                $image_name = time() . '-idcard-' . $data->id . '.' . $extension;
                
                // uploading...
                if (!$image->move($destinationPath, $image_name)){
                    return back()->withInput()->with('error', "Oops, failed to upload image KTP. Please try again or try upload another one.");
                }

                $data_details->seller_idcard = $image_name;
            }

            // unit_in_tkp
            if($request->hasFile('unit_in_tkp')){
                $image = $request->file('unit_in_tkp');
                $extension  = strtolower($image->getClientOriginalExtension());
                // format image name : XXXX-unit_tkp-[product_id].ext
                $image_name = time() . '-unit_tkp-' . $data->id . '.' . $extension;
                
                // uploading...
                if (!$image->move($destinationPath, $image_name)){
                    return back()->withInput()->with('error', "Oops, failed to upload image Unit in TKP. Please try again or try upload another one.");
                }

                $data_details->unit_in_tkp = $image_name;
            }

            // QC List
            $data_details->qc_status = $qc_status;
            $data_details->qc_list = null;
            if($qc_status != 1){
                $list = [];
                $destinationPath = public_path('uploads/product/qc/');
                for ($i=0; $i < count($request->qc_img); $i++) {
                    // upload image
                    if(isset($request->file('qc_img')[$i])){
                        $image = $request->file('qc_img')[$i];
                        $extension  = strtolower($image->getClientOriginalExtension());
                        // format image name : XXXX-qc_[no_urut]-[product_id].ext
                        $image_name = time() . '-qc_'.$i.'-' . $data->id . '.' . $extension;
                        
                        // uploading...
                        if (!$image->move($destinationPath, $image_name)){
                            return back()->withInput()->with('error', "Oops, failed to upload image QC item. Please try again or try upload another one.");
                        }

                        // set array data
                        $list[] = [
                            'image' => $image_name,
                            'description' => $request->qc_desc[$i],
                            'status' => 0
                        ];
                    }
                }

                $data_details->qc_list = json_encode($list);
            }

            // Modif
            $data_details->modif_status = $modif_status;
            $data_details->modif_list = null;
            if($modif_status != 1){
                $list = [];
                $destinationPath = public_path('uploads/product/modif/');
                for ($i=0; $i < count($request->modif_img); $i++) {
                    // upload image
                    if(isset($request->file('modif_img')[$i])){
                        $image = $request->file('modif_img')[$i];
                        $extension  = strtolower($image->getClientOriginalExtension());
                        // format image name : XXXX-modif_[no_urut]-[product_id].ext
                        $image_name = time() . '-modif_'.$i.'-' . $data->id . '.' . $extension;
                        
                        // uploading...
                        if (!$image->move($destinationPath, $image_name)){
                            return back()->withInput()->with('error', "Oops, failed to upload image modif item. Please try again or try upload another one.");
                        }

                        // set array data
                        $list[] = [
                            'image' => $image_name,
                            'description' => $request->modif_desc[$i],
                            'status' => 0
                        ];
                    }
                }

                $data_details->modif_list = json_encode($list);
            }

            $data_details->save();

            return redirect()->route('admin_product_list')->with('success', 'Successfully added a new product : '.$name);
        }

        return back()->withInput()->with('error', 'Oops, failed to add a new product. Please try again.');
    }

    public function edit($id)
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'View Details');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

        if((int) $id < 1){
            return redirect()->route('admin_product_list')->with('error', 'Product ID is invalid, please recheck your link again');
        }

        $data = Product::leftJoin('product_details AS dtl', 'dtl.prod_id', 'products.id')
            ->leftJoin('users', 'users.id', 'dtl.booked_by')
            ->leftJoin('customers', 'customers.id', 'dtl.booked_by_customer')
            ->leftJoin('transactions', 'transactions.product_id', 'products.id')
            ->where([
                'products.isDeleted' => 0,
                'products.id' => $id
            ])
            ->select('products.*', 'dtl.*', 'users.name as booked_name', 'customers.id as customer', 'transactions.nominal', 'transactions.description as note')
            ->first();

        if(!$data){
            return redirect()->route('admin_product_list')->with('error', 'Product not found, please recheck your link again');
        }

        $branches = Branch::where('status', 1)->where('isDeleted', 0)->get();
        $brands = Brand::where('status', 1)->where('isDeleted', 0)->get();
        $customers = Customer::where('status', 1)->where('isDeleted', 0)->get();

        return view('admin.product.form', compact('data', 'brands', 'branches', 'customers'));
    }

    public function do_edit($id, Request $request)
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'Edit');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

        $validation = [
            'seller_name' => 'required',
            'seller_phone' => 'required',
            'seller_idcard' => 'image|mimes:jpeg,png,jpg',
            'unit_in_tkp' => 'image|mimes:jpeg,png,jpg',
            'branch_id' => 'required|integer|min:1',
            'name' => 'required',
            'purchase_date' => 'required',
            'purchase_price' => 'required',
            // 'price_now' => 'required',
            'brand_id' => 'required|integer|min:1',
            'km' => 'required',
            'tax' => 'required',
            'qc_status' => 'required',
            'modif_status' => 'required'
        ];

        $message    = [
            'required' => ':attribute field is required'
        ];

        $names      = [
            'seller_name' => 'Seller Name',
            'seller_phone' => 'Seller Phone',
            'seller_idcard' => 'KTP',
            'unit_in_tkp' => 'Unit in TKP',
            'branch_id' => 'Branch',
            'name' => 'Name',
            'purchase_date' => 'Purchase Date',
            'purchase_price' => 'Purchase Price',
            'price_now' => 'Sell Price',
            'brand_id' => 'Brand',
            'km' => 'Kilometer',
            'tax' => 'Tax',
            'stock' => 'Stock',
            'qc_status' => 'QC List',
            'modif_status' => 'Modif'
        ];

        $this->validate($request, $validation, $message, $names);

        // sanitizing...
        $seller_name = Helper::validate_input_string($request->seller_name);
        if(!$seller_name){
            return back()->withInput()->with('error', 'Seller Name is required');
        }
        $seller_phone = Helper::validate_input_string($request->seller_phone);
        if(!$seller_phone){
            return back()->withInput()->with('error', 'Seller Phone is required');
        }
        $seller_bank_name = Helper::validate_input_string($request->seller_bank_name);
        $seller_bank_account = Helper::validate_input_string($request->seller_bank_account);
        $branch_id = (int) $request->branch_id;
        if($branch_id < 0){
            return back()->withInput()->with('error', "Branch is required, please choose one");
        }
        $name = Helper::validate_input_string($request->name);
        if(!$name){
            return back()->withInput()->with('error', 'Name is required');
        }
        $purchase_date = Helper::validate_input_string($request->purchase_date);
        $purchase_price = (int) str_replace(',', '', Helper::validate_input_string($request->purchase_price));
        $price_now = (int) str_replace(',', '', Helper::validate_input_string($request->price_now));
        $brand_id = (int) $request->brand_id;
        if($brand_id < 0){
            return back()->withInput()->with('error', "Brand is required, please choose one");
        }
        $km = Helper::validate_input_string($request->km);
        $tax = Helper::validate_input_string($request->tax);
        $stock = 1;
        if($stock < 0){
            return back()->withInput()->with('error', "You can't input minus value for Stock");
        }
        $plat_no = strtoupper(Helper::validate_input_string($request->plat_no));
        
        // QC List
        $qc_status = Helper::validate_input_string($request->qc_status);
        if($qc_status == 'yes_qc'){
            // perlu QC
            $qc_status = 0;
        }else{
            // lulus QC
            $qc_status = 1;
        }

        // Modif
        $modif_status = Helper::validate_input_string($request->modif_status);
        if($modif_status == 'yes_modif'){
            // ada modif
            $modif_status = 0;
        }else{
            // tidak ada modif
            $modif_status = 1;
        }

        // get existing data
        $data = Product::find($id);

        if(!$data){
            return back()->withInput()->with('error', 'Product not found, please reload your page before resubmit');
        }

        // update product table
        $data->name = $name;
        $data->brand_id = $brand_id;
        $data->stock = $stock;
        $data->price_now = $price_now;

        if($data->save()){
            // convert date
            $purchase_date_arr = explode('/', $purchase_date);
            $purchase_date = $purchase_date_arr[2].'-'.$purchase_date_arr[1].'-'.$purchase_date_arr[0];
            $tax_arr = explode('/', $tax);
            $tax = $tax_arr[2].'-'.$tax_arr[1].'-'.$tax_arr[0];

            $data_details = ProductDetails::find($id);
            $data_details->branch_id = $branch_id;
            $data_details->purchase_date = $purchase_date;
            $data_details->purchase_price = $purchase_price;
            $data_details->km = $km;
            $data_details->tax = $tax;

            $data_details->seller_name = $seller_name;
            $data_details->seller_phone = $seller_phone;
            $data_details->seller_bank_name = $seller_bank_name;
            $data_details->seller_bank_account = $seller_bank_account;
            $data_details->plat_no = $plat_no;

            // seller_idcard
            $destinationPath = public_path('uploads/product/seller/');
            if($request->hasFile('seller_idcard')){
                $image = $request->file('seller_idcard');
                $extension  = strtolower($image->getClientOriginalExtension());
                // format image name : XXXX-idcard-[product_id].ext
                $image_name = time() . '-idcard-' . $data->id . '.' . $extension;
                
                // uploading...
                if (!$image->move($destinationPath, $image_name)){
                    return back()->withInput()->with('error', "Oops, failed to upload image KTP. Please try again or try upload another one.");
                }

                // get existing data for remove after success updated
                $old_seller_idcard = $data_details->seller_idcard;

                // set new data
                $data_details->seller_idcard = $image_name;
            }

            // unit_in_tkp
            if($request->hasFile('unit_in_tkp')){
                $image = $request->file('unit_in_tkp');
                $extension  = strtolower($image->getClientOriginalExtension());
                // format image name : XXXX-unit_tkp-[product_id].ext
                $image_name = time() . '-unit_tkp-' . $data->id . '.' . $extension;
                
                // uploading...
                if (!$image->move($destinationPath, $image_name)){
                    return back()->withInput()->with('error', "Oops, failed to upload image Unit in TKP. Please try again or try upload another one.");
                }

                // get existing data for remove after success updated
                $old_unit_in_tkp = $data_details->unit_in_tkp;

                // set new data
                $data_details->unit_in_tkp = $image_name;
            }

            /* QC List - BEGIN */
            $data_details->qc_status = $qc_status;
            // get existing JSON data
            $list = json_decode($data_details->qc_list, true);
            if($qc_status != 1){
                // need to QC

                // if upload new image
                if(isset($request->qc_img)){
                    $destinationPath = public_path('uploads/product/qc/');
                    foreach ($request->qc_img as $key => $value) {
                        // upload image
                        $image = $request->file('qc_img')[$key];
                        $extension  = strtolower($image->getClientOriginalExtension());
                        // format image name : XXXX-qc_[no_urut]-[product_id].ext
                        $image_name = time() . '-qc_'.$key.'-' . $data->id . '.' . $extension;
                        
                        // uploading...
                        if (!$image->move($destinationPath, $image_name)){
                            return back()->withInput()->with('error', "Oops, failed to upload image QC item. Please try again or try upload another one.");
                        }
    
                        if(isset($list[$key]['image'])){
                            // get old image data
                            $qc_img_olds[] = $list[$key]['image'];
                        }else{
                            // set new partition for save data
                            $list[$key]['description'] = '';
                            $list[$key]['status'] = 0;
                        }
    
                        // save new image data
                        $list[$key]['image'] = $image_name;
                    }
                }

                // manipulating QC list description
                foreach ($request->qc_desc as $key => $value) {
                    $list[$key]['description'] = $value;
                }

                // check is deleting data
                if(!empty($request->qc_deleted)){
                    $qc_deleted = explode('|', $request->qc_deleted);
                    if(count($qc_deleted) > 0){
                        $destinationPath = public_path('uploads/product/qc/');
                        foreach ($qc_deleted as $item) {
                            // delete existing image
                            $old_img = $list[$item]['image'];
                            if(isset($old_img) && !empty($old_img) && file_exists($destinationPath.$old_img)){
                                unlink($destinationPath.$old_img);
                            }
                            // delete existing data
                            unset($list[$item]);
                        }
                    }
                }
                
                // save data in JSON format
                $data_details->qc_list = json_encode($list);
            }else{
                // NO NEED QC
                // delete existing data
                if(!empty($list)){
                    $destinationPath = public_path('uploads/product/qc/');
                    foreach ($list as $item) {
                        // delete existing image
                        $old_img = $item['image'];
                        if(isset($old_img) && !empty($old_img) && file_exists($destinationPath.$old_img)){
                            unlink($destinationPath.$old_img);
                        }
                    }
                }

                $data_details->qc_list = null;
            }
            /* QC List - END */

            /* Modif - BEGIN */
            $data_details->modif_status = $modif_status;
            // get existing JSON data
            $list = json_decode($data_details->modif_list, true);
            if($modif_status != 1){
                // modif is exist

                // if upload new image
                if(isset($request->modif_img)){
                    $destinationPath = public_path('uploads/product/modif/');
                    foreach ($request->modif_img as $key => $value) {
                        // upload image
                        $image = $request->file('modif_img')[$key];
                        $extension  = strtolower($image->getClientOriginalExtension());
                        // format image name : XXXX-modif_[no_urut]-[product_id].ext
                        $image_name = time() . '-modif_'.$key.'-' . $data->id . '.' . $extension;
                        
                        // uploading...
                        if (!$image->move($destinationPath, $image_name)){
                            return back()->withInput()->with('error', "Oops, failed to upload image modif item. Please try again or try upload another one.");
                        }
    
                        if(isset($list[$key]['image'])){
                            // get old image data
                            $modif_img_olds[] = $list[$key]['image'];
                        }else{
                            // set new partition for save data
                            $list[$key]['description'] = '';
                            $list[$key]['status'] = 0;
                        }
    
                        // save new image data
                        $list[$key]['image'] = $image_name;
                    }
                }

                // manipulating modif list description
                foreach ($request->modif_desc as $key => $value) {
                    $list[$key]['description'] = $value;
                }

                // check is deleting data
                if(!empty($request->modif_deleted)){
                    $modif_deleted = explode('|', $request->modif_deleted);
                    if(count($modif_deleted) > 0){
                        $destinationPath = public_path('uploads/product/modif/');
                        foreach ($modif_deleted as $item) {
                            // delete existing image
                            $old_img = $list[$item]['image'];
                            if(isset($old_img) && !empty($old_img) && file_exists($destinationPath.$old_img)){
                                unlink($destinationPath.$old_img);
                            }
                            // delete existing data
                            unset($list[$item]);
                        }
                    }
                }
                
                // save data in JSON format
                $data_details->modif_list = json_encode($list);
            }else{
                // NO modif
                // delete existing data
                if(!empty($list)){
                    $destinationPath = public_path('uploads/product/modif/');
                    foreach ($list as $item) {
                        // delete existing image
                        $old_img = $item['image'];
                        if(isset($old_img) && !empty($old_img) && file_exists($destinationPath.$old_img)){
                            unlink($destinationPath.$old_img);
                        }
                    }
                }

                $data_details->modif_list = null;
            }
            /* Modif List - END */

            if($data_details->save()){
                // remove old image(s) - seller details
                $destinationPath = public_path('uploads/product/seller/');
                if(isset($old_seller_idcard) && !empty($old_seller_idcard) && file_exists($destinationPath.$old_seller_idcard)){
                    unlink($destinationPath.$old_seller_idcard);
                }
                if(isset($old_unit_in_tkp) && !empty($old_unit_in_tkp) && file_exists($destinationPath.$old_unit_in_tkp)){
                    unlink($destinationPath.$old_unit_in_tkp);
                }
                
                // remove old image(s) - QC list
                $destinationPath = public_path('uploads/product/qc/');
                if(isset($qc_img_olds)){
                    if(count($qc_img_olds) > 0){
                        foreach ($qc_img_olds as $item) {
                            if(file_exists($destinationPath.$item)){
                                unlink($destinationPath.$item);
                            }
                        }
                    }
                }

                // remove old image(s) - modif list
                $destinationPath = public_path('uploads/product/modif/');
                if(isset($modif_img_olds)){
                    if(count($modif_img_olds) > 0){
                        foreach ($modif_img_olds as $item) {
                            if(file_exists($destinationPath.$item)){
                                unlink($destinationPath.$item);
                            }
                        }
                    }
                }

                return redirect()->route('admin_product_edit', $id)->with('success', 'Successfully updated purchase details product : '.$name);
            }

            return back()->withInput()->with('error', 'Oops, failed to updated purchase details of product. Please try again.');
        }
        return back()->withInput()->with('error', 'Oops, failed to updated product. Please try again.');
    }

    public function delete($id)
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'Delete');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

        if((int) $id < 1){
            return redirect()->route('admin_product_list')->with('error', 'Product ID is invalid, please try again');
        }

        $data = Product::find($id);

        if(!$data){
            return redirect()->route('admin_product_list')->with('error', 'Product not found, please try again');
        }

        $data->isDeleted = 1;

        if($data->save()){
            return redirect()->route('admin_product_list', $id)->with('success', 'Successfully deleted product : '.$data->name);
        }else{
            return back()->with('error', 'Oops, failed to delete product. Please try again.');
        }
    }

    public function list_deleted()
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'Restore');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

        $deleted = Product::leftJoin('product_details AS dtl', 'dtl.prod_id', 'products.id')
            ->leftJoin('branch', 'branch.id', 'dtl.branch_id')
            ->where([
                'products.isDeleted' => 1
            ])
            ->select('products.id', 'products.name', 'products.currency', 'products.stock', 'products.updated_at', 'products.status', 'products.images', 'products.image_primary', 'dtl.purchase_date', 'dtl.purchase_price', 'dtl.qc_status', 'dtl.photo_status', 'dtl.publish_status', 'branch.name as branch_name')
            ->orderBy('products.updated_at', 'desc')
            ->paginate(10);

        return view ('admin.product.list', compact('deleted'));
    }

    public function restore($id)
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'Restore');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

        if((int) $id < 1){
            return redirect()->route('admin_product_deleted')->with('error', 'Product ID is invalid, please try again');
        }

        $data = Product::find($id);

        if(!$data){
            return redirect()->route('admin_product_deleted')->with('error', 'Product not found, please try again');
        }

        $data->isDeleted = 0;

        if($data->save()){
            return redirect()->route('admin_product_deleted', $id)->with('success', 'Successfully restored product : '.$data->name);
        }else{
            return back()->with('error', 'Oops, failed to restore product. Please try again.');
        }
    }

    public function submit_qc($id, Request $request)
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'Do QC');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

        $requests = $request->all();
        if(count($requests) > 1){
            // get qc_list data
            $data = ProductDetails::find($id);

            if(!$data || $data->qc_list == ''){
                return back()->withInput()->with('error', 'QC list of this product not found, please reload your page before resubmit');
            }

            $qc_list = json_decode($data->qc_list, true);

            // upload image(s)
            if(isset($request->qc_img_after)){
                $destinationPath = public_path('uploads/product/qc/');
                if(count($request->qc_img_after) > 0){
                    foreach ($request->qc_img_after as $key => $value) {
                        $image = $request->file('qc_img_after')[$key];
                        $extension  = strtolower($image->getClientOriginalExtension());
                        // format image name : XXXX-qc_after_[no_urut]-[product_id].ext
                        $image_name = time() . '-qc_after_'.$key.'-' . $id . '.' . $extension;
                        
                        // uploading...
                        if (!$image->move($destinationPath, $image_name)){
                            return back()->withInput()->with('error', "Oops, failed to upload image After QC item. Please try again or try upload another one.");
                        }

                        // get existing image
                        if(isset($qc_list[$key]['image_after'])){
                            if(file_exists($destinationPath.$qc_list[$key]['image_after'])){
                                $img_olds[] = $qc_list[$key]['image_after'];
                            }
                        }

                        // save data
                        $qc_list[$key]['image_after'] = $image_name;
                    }
                }
            }

            $check_all = [];
            for ($i=0; $i < count($request->qc_list_status); $i++) {
                // save data
                $qc_list[$i]['status'] = $request->qc_list_status[$i];

                $check_all[] = $request->qc_list_status[$i];
            }
            
            // update status task list
            $data->qc_list = json_encode($qc_list);

            if(!in_array(0, $check_all)){
                $data->qc_status = 1;
            }else{
                $data->qc_status = 0;
            }

            if($data->save()){
                // delete old image if replace with new one
                if(isset($img_olds)){
                    foreach ($img_olds as $key => $value) {
                        unlink($destinationPath.$value);
                    }
                }

                return redirect()->route('admin_product_edit', $id.'#step-2')->with('success', 'Successfully updated Quality Control Task List in this product');
            }
            return back()->withInput()->with('error', 'Oops, failed to updated Quality Control Task List in this product. Please try again.');
        }
        return back()->withInput()->with('success', "You didn't update anything in Quality Control Task List in this product");
    }

    public function upload_photos($id, Request $request)
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'Upload Photos');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

        $requests = $request->all();
        if(count($requests) > 1){
            // get photos data
            $data = Product::find($id);

            if(!$data){
                return back()->withInput()->with('error', 'Product not found, please reload your page before resubmit');
            }

            // set destination path for uploaded file(s)
            $destinationPath = public_path('uploads/product/');

            $list = [];

            // get existing image data
            $exist = json_decode($data->images, true);
            if($exist){
                foreach ($exist as $key => $value) {
                    $list[$key] = $value;
                }
            }

            foreach ($requests as $key => $value) {
                if($key == '_token'){
                    continue;
                }
                
                $image = $request->file($key);

                if($image){
                    $extension  = strtolower($image->getClientOriginalExtension());
                    $image_name   = time() . '-' . uniqid() . '-main.' . $extension;
    
                    // uploading...
                    if ($image->move($destinationPath, $image_name)){
                        // check is replace existing data
                        if(array_key_exists($key, $list)){
                            // remove existing file
                            unlink($destinationPath.$list[$key]);
                        }
                        $list[$key] = $image_name;
                    }else{
                        return back()->withInput()->with('error', "Oops, failed to upload '".$key."' this product");
                    }
                }
            }

            $json_list = json_encode($list);

            $data->images = $json_list;

            if(isset($request->image_primary)){
                $image_primary = (int) $request->image_primary;
                if($image_primary > count($list)){
                    $image_primary = 1;
                }elseif($image_primary < 0){
                    $image_primary = 0;
                }
                $data->image_primary = $image_primary;
            }

            if($data->save()){
                $data_details = ProductDetails::find($id);
                $data_details->photo_status = 1;
                $data_details->save();

                return redirect()->route('admin_product_edit', $id.'#step-3')->with('success', 'Successfully upload photo(s) of this product');
            }
            return back()->withInput()->with('error', "Oops, failed to save image(s) path of this product");
        }
        return back()->withInput()->with('success', "You didn't upload photo(s) in this product");
    }

    public function publish($id, Request $request)
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'Publish');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

        $validation = [
            'display_name' => 'required',
            'summary' => 'required',
            'description' => 'required',
            'post_toped' => 'integer',
            'post_olx' => 'integer'
        ];

        $message    = [
            'required' => ':attribute field is required'
        ];

        $names      = [
            'display_name' => 'Display Name',
            'summary' => 'Summary',
            'description' => 'Description',
            'post_toped' => 'Post in Tokopedia',
            'post_olx' => 'Post in OLX'
        ];

        $this->validate($request, $validation, $message, $names);

        // sanitizing
        $display_name = Helper::validate_input_string($request->display_name);
        if(!$display_name){
            return back()->withInput()->with('error', 'Display Name is required');
        }
        $summary = Helper::validate_input_string($request->summary);
        if(!$summary){
            return back()->withInput()->with('error', 'Summary is required');
        }
        $description = Helper::validate_input_string($request->description);
        if(!$description){
            return back()->withInput()->with('error', 'Description is required');
        }
        $post_toped = (int) $request->post_toped;
        if($post_toped > 1){
            $post_toped = 1;
        }elseif($post_toped < 0){
            $post_toped = 0;
        }
        $post_olx = (int) $request->post_olx;
        if($post_olx > 1){
            $post_olx = 1;
        }elseif($post_olx < 0){
            $post_olx = 0;
        }

        // get existing data
        $data = Product::find($id);

        if(!$data){
            return back()->withInput()->with('error', 'Product not found, please reload your page before resubmit');
        }

        // update product table
        $data->summary = $summary;
        $data->description = $description;

        if($data->save()){
            $data_details = ProductDetails::find($id);
            $data_details->display_name = $display_name;
            $data_details->post_toped = $post_toped;
            $data_details->post_olx = $post_olx;
            $data_details->published_date = date('Y-m-d');
            $data_details->published_by = Session::get('admin')->id;
            $data_details->publish_status = 1;

            if($data_details->save()){
                return redirect()->route('admin_product_edit', $id.'#step-4')->with('success', 'Successfully published product : '.$display_name);
            }
            return back()->withInput()->with('error', 'Oops, failed to saved this product details. Please try again.');
        }
        return back()->withInput()->with('error', 'Oops, failed to published this product. Please try again.');
    }

    public function set_booked($id, Request $request)
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'Set Booked');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

        $validation = [
            'booked_date' => 'required',
            'customer' => 'required',
            'nominal' => 'required'
        ];

        $message    = [
            'required' => ':attribute field is required'
        ];

        $names      = [
            'booked_date' => 'Booked Date',
            'customer' => 'Customer',
            'nominal' => 'Nominal DP'
        ];

        $this->validate($request, $validation, $message, $names);

        // sanitizing...
        $booked_date = Helper::validate_input_string($request->booked_date);
        $cancel_booked = Helper::validate_input_string($request->cancel_booked);
        $customer = (int) $request->customer;
        if ($customer < 1)
        {
            return back()->withInput()->with('error', 'Customer must be selected at least one');
        }
        // check customer is exist
        $get_customer = Customer::find($customer);
        if (!$get_customer)
        {
            return back()->withInput()->with('error', 'Customer not found, please reload your page before resubmit');
        }
        $nominal = (int) str_replace(',', '', Helper::validate_input_string($request->nominal));
        $description = Helper::validate_input_string($request->note);

        // convert date
        $tmp_date_arr = explode('/', $booked_date);
        $booked_date = $tmp_date_arr[2].'-'.$tmp_date_arr[1].'-'.$tmp_date_arr[0];

        // get existing data
        $data_details = ProductDetails::find($id);

        if(!$data_details){
            return back()->withInput()->with('error', 'Product not found, please reload your page before resubmit');
        }

        // update product table
        $data_details->booked_date = $booked_date;
        $data_details->booked_by = Session::get('admin')->id;
        $data_details->booked_by_customer = $customer;
        $statement = 'booked';

        // CANCEL BOOKING
        if($cancel_booked == 'yes')
        {
            $data_details->booked_date = null;
            $data_details->booked_by = null;
            $data_details->booked_by_customer = null;
            $statement = 'cancel booked';
        }

        if ($data_details->save())
        {
            if ($cancel_booked != 'yes')
            {
                // record into transaction
                $transaction = Transaction::updateOrCreate(
                    ['product_id' => $id, 'status' => 1],
                    ['date_transaction' => $booked_date, 'nominal' => $nominal, 'description' => $description]
                );

                if (!$transaction)
                {
                    return back()->withInput()->with('error', 'Oops, failed to record transaction this product. Please try again.');
                }
            }

            return redirect()->route('admin_product_edit', $id.'#step-5')->with('success', 'Successfully '.$statement.' a product : '.$data_details->display_name);
        }

        return back()->withInput()->with('error', 'Oops, failed to '.$statement.' a product. Please try again.');
    }
}
