<?php

namespace Respins\BaseFunctions\Facades;

use Illuminate\Support\Facades\Facade;

class ProxyHelperFacade extends Facade {

    protected static function getFacadeAccessor(){
        return "ProxyHelper";
    }
}