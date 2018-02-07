<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
class Authenticate
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
        if (!Auth::check()) {
            $uri = $_SERVER['REQUEST_URI'];
            //echo $uri;

            return redirect()->guest('/login#'.$uri);
        }

        return $next($request);
    }
}
