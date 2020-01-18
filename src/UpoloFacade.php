<?php

namespace Hamidrezaniazi\Upolo;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Hamidrezaniazi\Upolo\Skeleton\SkeletonClass
 */
class UpoloFacade extends Facade
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
