<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UserTypeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $user_type)
    {
        $user_types = explode("|", $user_type);
        if(in_array(auth()->user()->user_type, $user_types)){
            return $next($request);
        }
        abort(404);
    }
}
