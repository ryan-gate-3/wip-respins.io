<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Respins\BaseFunctions\Controllers\API\SessionController;
use Respins\BaseFunctions\Controllers\Game\BlueoceanController;
use Respins\BaseFunctions\Controllers\EndpointRouter;
use Respins\BaseFunctions\Controllers\Data\GameImporterController;
use Respins\BaseFunctions\Controllers\FrontendController;
use Respins\BaseFunctions\Controllers\Admin\AdminPageController;
use Respins\BaseFunctions\BaseFunctions;
use Respins\BaseFunctions\Controllers\Livewire\GamesLauncher;
use Respins\BaseFunctions\Controllers\Livewire\GamesList;
use Respins\BaseFunctions\Controllers\Livewire\MaintenancePanel;


## Base.php
# If using system across different domains/hosts or productionalm, make sure to read general explaination regarding laravel routing/mw:
# Laravel Middleware @ https://laravel.com/docs/9.x/middleware

# API Middleware 
# Headerless data (without frontend) under most circumstances will be JSON data, but can also be form-data, XML data or whatever is needed.
Route::middleware('api', 'throttle:500,1')->prefix('api/respins.io')->group(function () {

    ## Data group
    Route::group(['namespace' => 'data', 'prefix' => 'data'], function() {
        Route::get('/game-importer', [GameImporterController::class, 'gamelistImporter']);
    });
    ## Game aggregation group    
    Route::group(['namespace' => 'aggregation', 'prefix' => 'aggregation'], function() {
        Route::get('/createSession', [EndpointRouter::class, 'createSessionEndpoint']);
    });
    Route::group(['namespace' => 'testing', 'prefix' => 'testing'], function() {
        Route::get('/migrate', [BaseFunctions::class, 'migrate']);
        Route::get('/testingRedirect', [BaseFunctions::class, 'testingRedirect']);
    });

    

});




## Web middleware
# Middleware to use when in need to 'catch' legitimate player request data to forward using our proxy helpers or to display frontend pre-auth on casino level.
Route::middleware('web', 'throttle:2000,1')->prefix('web/respins.io')->group(function () {

    ## Admin Frontend
    Route::group(['namespace' => 'admin', 'prefix' => 'admin'], function() {
        Route::get('{slug}', [AdminPageController::class, 'enter']);
    });

    ## Games List
    #Route::get('/games/{slug}', [FrontendController::class, 'indexGames']);

    ## Launcher
    Route::get('/games/launch/{slug}', GamesLauncher::class);
    Route::get('/games/list', GamesList::class)->name('gameslist');
    Route::get('/games/error', [FrontendController::class, 'gameErrorPage']);
    Route::get('/maintenance-panel', MaintenancePanel::class)->name('maintenance-panel');


    
    ## Game Entry
    Route::get('/entrySession', [SessionController::class, 'entrySession']);


});
