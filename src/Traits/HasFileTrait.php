<?php

namespace Hamidrezaniazi\Upolo\Traits;

use Hamidrezaniazi\Upolo\Models\File;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasFileTrait
{
    /**
     * @return MorphMany
     */
    public function files(): MorphMany
    {
        return $this->morphMany(File::class, 'owner');
    }
}
