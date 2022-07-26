<?php

namespace Respins\BaseFunctions\Middleware;
use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;

class RespinsIPCheck
{
    # You can change the access IP (singular) on ipv4 on baseconfig in config folder
    # If you use Cloudflare, set cloudflare_mode to true in config
    public function handle(Request $request, Closure $next ) {
        $cf_mode = config('baseconfig.access.cloudflare_mode');

        if($request->ip() !== config('baseconfig.access.admin_ip')) {
            $response = [
                'status' => 403,
                'message' => 'Route middleware protected. Change by configuring your IPV4 IP address within package configuration file.',
                'ip_native' => $request->ip(),
                'ip_cf' => $request->header('CF-Connecting-IP'),
                'cf_mode' => $cf_mode,
            ];
            return response()->json($response, 403);

        }
        return $next($request);

    }
}
 