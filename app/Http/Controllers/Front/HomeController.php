<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Banner;
use Session;

// library
use App\Libraries\Helper;

class HomeController extends Controller
{
    public function index()
    {
        $banner = Banner::where('status', 1)->where('isDeleted', 0)->orderBy('order')->get();
        return view('front.home', compact('banner'));
    }

    public function login()
    {
        if(Session::get('customer')){
            return redirect()->route('home');
        }
        return view('front.login');
    }

    public function do_login(Request $request)
    {
        $validation = [
            'login_id' => 'required|email',
            'login_pass' => 'required'
        ];

        $message    = [
            'required' => ':attribute should not be empty'
        ];

        $names      = [
            'login_id' => 'Email',
            'login_pass' => 'Password'
        ];

        $this->validate($request, $validation, $message, $names);

        $customer = Customer::where([
                'email' => Helper::validate_input_email($request->login_id),
                'password' => Helper::hashing_this($request->login_pass)
            ])
            ->select('id', 'name', 'email', 'isDeleted', 'status')
            ->first();
        
        if ($customer) {
            if ($customer->isDeleted != 0) {
                return back()->withInput($request->flashExcept('login_pass'))->with('error', 'Login failed! Because your account has been deleted!');
            }
            
            if($customer->status != 1){
                return back()->withInput($request->flashExcept('login_pass'))->with('error', 'Login failed! Because your account has been disabled!');
            }

            // success login
            return redirect()->route('home')->with(Session::put('customer', $customer));
        }else{
            return back()->withInput($request->flashExcept('login_pass'))->with('error', 'Username or Password is wrong!');
        }
    }

    public function logout()
    {
        Session::forget('customer');
        return redirect()->route('home')->with('success', 'Logout successfully');
    }
}