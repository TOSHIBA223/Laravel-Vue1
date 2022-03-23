<?php
namespace App\Http\Middleware;

use Closure;
use \Symfony\Component\HttpFoundation\IpUtils;
use App\Models\AdminIpWhitelist as Whitelist;

class HasSession
{

    public function handle($request, Closure $next)
    {
        if( !\Auth::check() )
            return $next($request);

        return \Redirect::route('dashboard');
    }
}
