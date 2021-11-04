<?php

namespace App\Http\Middleware;

use App\Repositories\APIKeyRepo;
use App\Traits\ResponseTrait;
use Closure;
use Illuminate\Http\Request;

class ApiMiddleware
{
    use ResponseTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $apikeyRepo = new APIKeyRepo;
        $key    = explode(' ',$request->header('Authorization'))[1];
        $ip     = $request->ip();
        $apikey =  $apikeyRepo->findBy('api_key', $key, true);

        if(!$apikey) return $this->bad('Unauthenticated', 401, 'Invalid API Key');
        if(!$apikey->is_active) return $this->bad('Unauthenticated', 401, 'Api Key is not active');

        if($apikey->is_strict_ip) : 
            if(!in_array($ip, $apikey->whitelist_ip)) {
                return $this->bad('Unauthenticated', 401);
            }
        endif;
        
        return $next($request);
    }
}
