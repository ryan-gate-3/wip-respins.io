<?php
namespace Respins\BaseFunctions\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use DB;
use Respins\BaseFunctions\BaseFunctions;
use Respins\BaseFunctions\Traits\ApiResponseHelper;
use Respins\BaseFunctions\Models\RawGameslist;
use Respins\BaseFunctions\Models\Gameslist;

class DataController
{
    use ApiResponseHelper;

    public static function getProviders()
    {   
        $cache_length = config('baseconfig.caching.length_getProvider');
        if($cache_length === 0) {
            return RawGameslist::rawProviders();
        }

        $value = Cache::remember('cache:getProviders', $cache_length, function () {
            return RawGameslist::rawProviders();
        });
        return $value;
    }

    public static function getGames()
    {   
        $cache_length = config('baseconfig.caching.length_getGames');
        
        if($cache_length === 0) {
            return RawGameslist::all();
        }
        $value = Cache::remember('raw:getGames', $cache_length, function () {
            return RawGameslist::all();
        });

        return $value;
    } 

    public static function selectProvider($provider = NULL)
    {
        if($provider === NULL) {
            return false;
        }

        //Retrieving providers, which also triggers the cache on the function calling this way
        $retrieve = self::getProviders();
        if($retrieve === NULL) { 
            return false;
        }
        $collection = collect(json_decode($retrieve));
        $select_provider = $collection->where('slug', $provider)->first();

        if($select_provider !== NULL) {
            $merge = array('count' => self::$model_games);
            $m = $select_provider->merge($merge);
            return $m;
        } else {
            return false;
        }
    }

}
