<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            if (auth()->user()->hasRole('admin')) {
                return redirect()->route('user.activities');
            } else if (auth()->user()->hasRole('operator')) {
                return redirect()->route('seller.list');
            } else if (auth()->user()->hasRole('seller')) {
                return redirect()->route('sellerProductList');
            } else if (auth()->user()->hasRole('vendor')) {
                return redirect()->route('localvendorProductList');
            } else {
                return redirect()->route('buyer.top');
            }
        }

        return $next($request);
    }
}
