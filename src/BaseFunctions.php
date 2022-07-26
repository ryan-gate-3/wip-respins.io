<?php

namespace Respins\BaseFunctions;
use Respins\BaseFunctions\Models\Players;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class BaseFunctions
{
    
    // Helper intended to parse "in_between" values mainly for html content (resource intensive)
    public static function in_between($a, $b, $data) 
    {
        preg_match('/'.$a.'(.*?)'.$b.'/s', $data, $match);
        if(!isset($match[1])) {
            return false;
        }
        return $match[1];
    }

    // Generating security signature - using hmac signing, you can change algo's however MD5 is fastest
    // https://www.php.net/manual/en/function.hash-hmac.php
    public static function generate_sign($token, $pwd = NULL) 
    {
        $timestamp = time(); 
        if($pwd === NULL) {
            $pwd = config('gameconfig.api_settings.signature_password');
        }
        
        $encryption_key = $pwd.'-'.$timestamp; //Consider timestamp the randomizing salt, can be replaced by any randomizing key/regex

        $generate_sign = hash_hmac('md5', $token, $encryption_key);
        $concat_sign_time = $generate_sign.'-'.$timestamp;
        return $concat_sign_time;
    }

    public static function verify_sign($signature, $token, $pwd = NULL)
    {
        
        if($pwd === NULL) {
            $pwd = config('gameconfig.api_settings.signature_password');
        }

        try {
            $explode_signature = explode('-', $signature); 
            $timestamp = $explode_signature[1];
            $encryption_key =  $pwd.'-'.$timestamp;
            $generate_sign = hash_hmac('md5', $token, $encryption_key);
            $concat_sign_time = $generate_sign.'-'.$timestamp;
            if($signature === $concat_sign_time) { // verify signature is same outcome
                return true;
            }
        } catch (\Exception $exception) {
            return false;
        }


        return false; //signature not matching, returning false


    }

    public static function messageHelper($message)
    {
        $message = array('message' => $message);
        return $message;
    }

    public static function helperGamelink() {
        if(config('baseconfig.frontend.launcher_location') === 'local') {
            return '/';
        } else {
            return config('baseconfig.frontend.launcher_location');
        }
    } 

    public static function migrate() 
    {
        // Disabled
        //return 'disabled';
   
        
        $migrate = \Artisan::call('migrate:fresh');
        $cache_clear = \Artisan::call('cache:clear');
        $import_games = \Respins\BaseFunctions\Controllers\Data\GameImporterController::seed_development();
        return $import_games;
    } 

    public static function morphToArray($data)
    {
        if ($data instanceof Arrayable) {
            return $data->toArray();
        }

        if ($data instanceof JsonSerializable) {
            return $data->jsonSerialize();
        }

        return $data;
    }


    # Error routing
    # Usage Example: return \Respins\BaseFunctions\BaseFunctions::errorRouting(401, 'Failed to create player.');
    public static function errorRouting($statuscode, $message = NULL, $errorType = NULL, $data = NULL)
    {
        //Array with meta
        if($message !== NULL) {
            $message = array(
                'status' => $statuscode,
                'message' => $message,
                'type' => $errorType,
                'data' => $data,
            );
        } else {
                $message = array(
                'status' => $statuscode,
                'message' => $message,
                'type' => $errorType,
                'data' => $data,
                );
        }

        #Operator Error Page
        // Operator level error page (casino)
        if($errorType === 'operator') {
            return view('respins::error-operator-template')->with('error', $message);
        }

        #Game Provider Error Page
        // Per game provider erroring
        if($errorType === 'gameprovider') {
            return view('respins::error-gameprovider-template')->with('error', $message);
        }

        #Fallback Error Page
        // Error page that is used if nothing is used
        return view('respins::error-default-template')->with('error', $message);
    }


}



