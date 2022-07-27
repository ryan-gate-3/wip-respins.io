<?php

namespace Respins\BaseFunctions\Models;

use \Illuminate\Database\Eloquent\Model as Eloquent;
use DB;
class WhitelistIPs extends Eloquent  {

    public static function collect_ips(){
        $query = config('baseconfig.access.whitelist_ips');
        $str_arr = explode (",", $query); 

        foreach($str_arr as $ip) {
            $exploded_ip = explode(':', $ip);
            $ip_array[] = array(
                'ip' => $exploded_ip[0],
                'type' => $exploded_ip[1],
            );
        }
        return $ip_array;
    }
    
    
}

