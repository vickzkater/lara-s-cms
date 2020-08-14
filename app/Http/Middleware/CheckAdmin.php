<?php

namespace App\Http\Middleware;

use Closure;
use Session;

class CheckAdmin
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
        if (Session::has('admin')) {
            return $next($request);
        } else {
            $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            
            if (strpos($actual_link, '/get-data') !== false) {
                // FOUND
                $actual_link = route('admin.home');
            }
            
            Session::put('redirect_uri', $actual_link);

            if ($actual_link == route('admin.home')) {
                return redirect()->route('admin.login');
            }

            return redirect()->route('admin.login')->with('error', lang('You must login first!'));
        }
    }
}
