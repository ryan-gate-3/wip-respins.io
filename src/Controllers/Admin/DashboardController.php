<?php
namespace Respins\BaseFunctions\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DashboardController
{
    public static function icon()
    {
        $icon_sidebar = '<svg
        class="w-6 h-6 text-gray-400"
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
        >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
        />
        </svg>';
        
        return $icon_sidebar;
    }

    public static function meta() 
    {      
        // Here you can return any data, info, statics, functions or variables you need to scaffold your page blade
        $meta = [
            'operators' => \App\Models\User::count(),
            'players' => \Respins\BaseFunctions\Models\Players::count(),
            'gamesessions' => \Respins\BaseFunctions\Models\GameSessions::count(),
            'game-importer-source' => 'bets.io', // to be refactored later when making the game importer modular
        ];
        return $meta;
    }

    // Hardcoded settings, safest is to keep hardcoded but you can put within repository/collection
    public static function actions()
    {
        $actions = 
        [
            [
                'key' => 'setting1',
                'command' => 'test',
                'cooldown' => '30',
                'descr' => 'Setting test text'
            ],
            [
                'key' => 'setting2',
                'command' => 'test2',
                'cooldown' => '30',
                'descr' => 'Setting test2 text'
            ],
            [
                'key' => 'setting3',
                'command' => 'test3',
                'cooldown' => '30',
                'descr' => 'Setting test3 text'
            ]
        ];
        return $actions;
    }



}