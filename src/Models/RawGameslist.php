<?php

namespace Respins\BaseFunctions\Models;

use \Illuminate\Database\Eloquent\Model as Eloquent;
use DB;
class RawGameslist extends Eloquent  {
    protected $table = 'respins_gamelist_raw';
    protected $timestamp = true;
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'gid',
        'slug',
        'name',
        'provider',
        'type',
        'typeRating',
        'popularity',
        'bonusbuy',
        'jackpot',
        'demoplay',
        'internal_origin_demolink',
        'internal_origin_casino',
        'internal_origin_realmoney',
        'internal_origin_rawobject',
        'internal_enabled',
        'created_at',
        'updated_at'
    ];  
    protected $casts = [
    'internal_origin_realmoney' => 'json',
    'internal_origin_rawobject' => 'json'
    ];

    public function link(){
        $url = config('baseconfig.frontend.launcher_url') ?? 'http://localhost';
        $path = config('baseconfig.frontend.launcher_path') ?? 'play-game';
        $complete_link = $url.'/'.$path.'/'.$this->slug;
        return $complete_link;
    }

    public static function rawProviders(){
        $query = DB::table('respins_gamelist_raw')->distinct()->get('provider');

        $providers_array[] = array();
        foreach($query as $provider) {
            $provider_array[] = array(
                'slug' => $provider->provider,
                'provider' => $provider->provider,
                'name' => ucfirst($provider->provider),
                'methods' => 'demoModding',
            );
        }

        return json_encode($provider_array, true);
    }
    
    
}

