<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

// MODELS
use App\Models\Product;

class SiteController extends Controller
{
    public function home()
    {
        $page = 'home';

        return view('web.home', compact('page'));
    }

    public function about()
    {
        $page = 'about';

        return view('web.about', compact('page'));
    }

    public function products()
    {
        $page = 'products';

        $data = Product::all();

        return view('web.products', compact('page', 'data'));
    }

    public function store()
    {
        $page = 'store';

        return view('web.store', compact('page'));
    }
}
