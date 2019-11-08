<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;

class ProductController extends Controller
{
    public function list()
    {
        return view('front.products.list');
    }

    public function detail($id)
    {
        if((int) $id < 1){
            return redirect()->route('home')->with('error', 'Link produk yang dituju invalid atau salah, mohon periksa kembali atau silahkan cari produk menarik lainnya');
        }

        $data = Product::leftJoin('brands', 'brands.id', '=', 'products.brand_id')
            ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
            ->where('products.id', (int) $id)
            ->select('products.*', 'brands.name as brand', 'categories.name as category')
            ->first();

        if(!$data){
            return redirect()->route('home')->with('error', 'Produk yang dituju mungkin telah dihapus atau dinon-aktifkan, silahkan cari produk menarik lainnya');
        }

        return view('front.products.detail', compact('data'));
    }
}
