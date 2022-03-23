<?php
namespace App\Http\Middleware;

use Closure;
use \Symfony\Component\HttpFoundation\IpUtils;
use App\Models\IPWhitelist as Whitelist;

class VerifyIp
{

    public function handle($request, Closure $next)
    {
        if( request()->ip() === '127.0.0.1' )
            return $next($request);

        $whitelist = Whitelist::getWhitelist();
        $user_ip = request()->ip();

        if(IpUtils::checkIp($user_ip, $whitelist))
            return $next($request);

        return redirect()->route('login')->with(
            ['incorrectIp' => 'Your IP Address does not have permission to access this site.']
        );
    }
}
