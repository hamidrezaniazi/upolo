<?php

namespace Hamidrezaniazi\Upolo\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FileResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return array_merge(parent::toArray($request), [
            'owner'   => $this->owner,
            'creator' => $this->creator,
        ]);
    }
}
