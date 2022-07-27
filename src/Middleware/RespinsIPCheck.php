<?php

namespace Respins\BaseFunctions\Middleware;
use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Respins\BaseFunctions\Controllers\DataController;

class RespinsIPCheck
{
    # You can change the access IP (singular) on ipv4 on baseconfig in config folder
    # If you use Cloudflare, set cloudflare_mode to true in config
    public function handle(Request $request, Closure $next ) {
        $whitelisted_ips = DataController::getWhitelistIPs();
        $check_ip = $request->ip();
        $select_ip = $whitelisted_ips->where('ip', $check_ip)->where('type', 'direct')->first();

        if($select_ip) {
            return $next($request);
        }

        $check_ip = $request->header('CF-Connecting-IP');
        $select_ip = $whitelisted_ips->where('ip', $check_ip)->where('type', 'cloudflare')->first();

        if($select_ip) {
            return $next($request);
        }

        $response = [
            'status' => 403,
            'message' => 'Route middleware protected. Add IP to \'/casino\'/config\'/baseconfig.php and flush cache if using cache.',
            'ip_native' => $request->ip(),
            'ip_cf' => $request->header('CF-Connecting-IP'),
        ];
        return response()->json($response, 403);


    }
}
 