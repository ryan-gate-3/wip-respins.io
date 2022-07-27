    <?php
// config for Respins/BaseFunctions
return [

    'access' => [
        'url' => env('APP_URL'),
        'admin_ip' => '109.37.128.201',
        'whitelist_ips' => '109.37.128.201:cloudflare,109.37.128.202:direct', // whitelist ips for access to the app in format "ip:type", type is either direct or cloudflare
    ],

    'caching' => [ // Caching length is in seconds, most caching is used within DataController. Advise is to set most caching options to 300 (5 minutes).
        'length_getWhitelistIPs' => 60,
        'length_getProvider' => 0,
        'length_getGames' => 0,
    ],

    'frontend' => [
        'theme' => 'jetstream', // Set to 'default' or if you use wave set to 'wave'
        'thumbnail_cdn' => 'https://cdn2.softswiss.net/arlekincasino/i/s2/',
        'include' => "@extends('theme::layouts.app')",
        'launcher_url' => env('APP_URL', 'localhost'),
        'launcher_path' => 'web/respins.io/games/launch',
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
