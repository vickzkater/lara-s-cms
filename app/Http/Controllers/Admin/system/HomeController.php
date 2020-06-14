<?php

namespace App\Http\Controllers\Admin\system;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index()
    {
        return view('admin.system.dashboard');
    }
}
