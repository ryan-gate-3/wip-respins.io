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
            $this->app->router->pushMiddlewareToGroup('web', \Respins\BaseFunctions\Middleware\OverrideHome::class);
            $this->loadLivewireComponents();

     }


    private function loadLivewireComponents()
    {
        Livewire::component('navigation-bar', \Respins\BaseFunctions\Controllers\Livewire\NavigationBar::class); 
        Livewire::component('dashboard', \Respins\BaseFunctions\Controllers\Livewire\NavigationBar::class); 
        Livewire::component('datalogger-viewer', \Respins\BaseFunctions\Controllers\Livewire\DataLoggerViewer::class); 
    }
}

 