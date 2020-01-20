<?php

namespace Hamidrezaniazi\Upolo\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface HasFileInterface
{
    /**
     * @return MorphMany
     */
    public function files(): MorphMany;
}
