<?php

namespace Hamidrezaniazi\Upolo;

use Illuminate\Support\Facades\Facade;

class Upolo extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'upolo';
    }
}
