<?php

namespace Respins\BaseFunctions;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Respins\BaseFunctions\Commands\ImporterSQL;
use Respins\BaseFunctions\ProxyHelper;
use Illuminate\Contracts\Http\Kernel;
use Livewire\Livewire;

class BaseFunctionsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {   
        //Register package functions
        $package
            ->name('base-functions')
            ->hasConfigFile(['baseconfig', 'adminconfig', 'gameconfig'])
            ->hasViews('respins')
            ->hasRoutes('base', 'game', 'jetstream')
            ->hasMigrations(['create_gamesessions_table', 'create_players_table', 'create_rawgameslist_table'])
            ->hasCommand(ImporterSQL::class);

            //Register the proxy
            $this->app->bind('ProxyHelper', function($app) {
                return new ProxyHelper();
            });

            $this->app->router->pushMiddlewareToGroup('web', \Respins\BaseFunctions\Middleware\RespinsIPCheck::class);
            $this->app->router->pushMiddlewareToGroup('api', \Respins\BaseFunctions\Middleware\RespinsIPCheck::class);
            $this->loadLivewireComponents();

     }


    private function loadLivewireComponents()
    {
        Livewire::component('navigation-bar', \Respins\BaseFunctions\Controllers\Livewire\NavigationBar::class); 
        Livewire::component('games-launcher', \Respins\BaseFunctions\Controllers\Livewire\GamesLauncher::class); 
        Livewire::component('games-list', \Respins\BaseFunctions\Controllers\Livewire\GamesList::class); 
        Livewire::component('maintenance-clear-cache', \Respins\BaseFunctions\Controllers\Livewire\MaintenancePanel::class); 

        

    }
}

 