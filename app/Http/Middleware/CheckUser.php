<?php

namespace App\Http\Middleware;

use Closure;
use Session;

class CheckUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Session::has('user')) {
            return $next($request);
        }else{
            $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            Session::put('redirect_uri_web', $actual_link);

            return redirect()->route('web.login')->with('warning', lang('You must login first!'));
        }
        
    }
}
