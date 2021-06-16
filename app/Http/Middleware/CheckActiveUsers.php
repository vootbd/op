<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckActiveUsers
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
        $user = Auth::user();
        if(isset($user) && $user->is_active == 0){
            auth()->logout();
            $request->session()->invalidate();
            return redirect()->route('login');
        }
        return $next($request);
    }
}
