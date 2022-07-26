<?php
// config used on installer & maintenance
return [

    'pages' => [
        [
            'slug' => 'dashboard',
            'title' => 'Dashboard',
            'description' => 'Simple extendable admin panel',
            'icon' => \Respins\BaseFunctions\Controllers\Admin\DashboardController::icon(),
            'controller' => \Respins\BaseFunctions\Controllers\Admin\DashboardController::class,
            'view' => 'respins::admin.dashboard',
        ],
        [
            'slug' => 'players',
            'title' => 'Players',
            'description' => 'Overview of players',
            'icon' => \Respins\BaseFunctions\Controllers\Admin\PlayersController::icon(),
            'controller' => \Respins\BaseFunctions\Controllers\Admin\PlayersController::class,
            'view' => 'respins::admin.players',
        ],
        [
            'slug' => 'template-cheatsheet',
            'title' => 'Cheatsheet',
            'description' => 'Cheatsheet to assist in creating admin blade views.',
            'icon' => \Respins\BaseFunctions\Controllers\Admin\TemplateCheatsheetController::icon(),
            'controller' => \Respins\BaseFunctions\Controllers\Admin\TemplateCheatsheetController::class,
            'view' => 'respins::admin.template-cheatsheet',
        ],

    ],


     
];
