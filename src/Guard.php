<?php

namespace Hamidrezaniazi\Upolo;

use Illuminate\Support\Facades\Config;

class Guard
{
    /**
     * Return guard model class name.
     *
     * @return string
     */
    public static function getGuardClassName(): string
    {
        $guard = Config::get('auth.defaults.guard');

        return collect(config('auth.guards'))
            ->map(function ($guard) {
                if (! isset($guard['provider'])) {
                    return;
                }

                return config("auth.providers.{$guard['provider']}.model");
            })->get($guard);
    }
}
