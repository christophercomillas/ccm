<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class UserType
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
        if(Auth::user()->usertype->usertype_name != 'Admin')
        {
            //echo Auth::user()->usertype['user_type_name'];
            return redirect('home');
        }

        return $next($request);
    }
}
