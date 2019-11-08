<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Product;

// library
use App\Libraries\Helper;

class IncomingController extends Controller
{
    // set this module
    private $module = 'Incoming';

    public function list()
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'View List');
        if($authorize['status'] != 'true'){
            return back()->with('error', $authorize['message']);
        }

        $data = Product::leftJoin('product_details AS dtl', 'dtl.prod_id', 'products.id')
            ->leftJoin('branch', 'branch.id', 'dtl.branch_id')
            ->where([
                'products.isDeleted' => 0,
                'products.price_now' => 0
            ])
            ->select('products.id', 'products.name', 'products.currency', 'products.stock', 'products.created_at', 'products.updated_at', 'products.status', 'products.images', 'products.image_primary', 'dtl.unit_in_tkp', 'dtl.purchase_date', 'dtl.purchase_price', 'dtl.seller_name', 'dtl.photo_status', 'dtl.publish_status', 'branch.name as branch_name')
            ->orderBy('products.updated_at', 'desc')
            ->paginate(10);

        return view ('admin.incoming.list', compact('data'));
    }
}
