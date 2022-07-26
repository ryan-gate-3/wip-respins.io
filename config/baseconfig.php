    <?php
// config for Respins/BaseFunctions
return [

    'access' => [
        'cloudflare_mode' => false, // Used for IP retrieval
        'lock_web' => true, // Locks down web routes to only server & admin IP access
        'lock_api' => true, //Locks down api routes to only server & admin IP access
        'admin_ip' => '172.21.0.2',
        'url' => env('APP_URL'),
    ],

    'caching' => [ // Caching length options are in seconds, followed by the function it is used
        'length_getProvider' => 1,
        'length_getGames' => 1,
    ],

    'frontend' => [
        'theme' => 'jetstream', // Set to 'default' or if you use wave set to 'wave'
        'thumbnail_cdn' => 'https://cdn2.softswiss.net/arlekincasino/i/s2/',
        'include' => "@extends('theme::layouts.app')",
        'launcher_url' => env('APP_URL', 'localhost'),
        'launcher_path' => 'respins.io/play-launcher',
        'gameslist' => [
            'games_per_page' => '24',
            'show_provider_nav' => true,
            'show_header' => true,
            'show_extended_gameinfo' => true,
            'show_object' => true,
        ],
    ],

    'host' => [
    	
    ],

    'game_config' => [
        
    ],

];
