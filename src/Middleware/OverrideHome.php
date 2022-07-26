<?php

namespace Respins\BaseFunctions\Middleware;
use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;

class OverrideHome
{
    # You can change the access IP (singular) on ipv4 on baseconfig in config folder
    # If you use Cloudflare, set cloudflare_mode to true in config
    public function handle(Request $request, Closure $next ) {

        $route = request()->route()->uri;

        return $route;

    }
}
 