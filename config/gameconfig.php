<?php
// Config for internal game (grey) handling
return [

    'session_entry_url' => env('APP_URL').'/web/respins.io/entrySession',

    'bgaming' => [
        'new_api_endpoint' => env('APP_URL').'/api/respins.io/games/bgaming/',
        'controller' => \Respins\BaseFunctions\Controllers\Game\BgamingController::class,
        'launcher_behaviour' => 'internal_game', // 'internal_game' or 'redirect' - expecting url on 'redirect' on controller::requestSession()
    ],

    'api_settings' => [
        'signature_password' => 'signature559',
    ],

];
