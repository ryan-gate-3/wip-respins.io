<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Respins\BaseFunctions\Controllers\EndpointRouter;
use Respins\BaseFunctions\Controllers\Game\BlueoceanController;
use Respins\BaseFunctions\Controllers\Data\GameImporterController;
use Respins\BaseFunctions\Controllers\FrontendController;
use Respins\BaseFunctions\Controllers\Admin\AdminPageController;

Route::middleware('web', 'throttle:2000,1')->group(function () {
    Route::get('/dashboard', [Dashboard::class, 'indexGames'])->name('dashboard');

});
