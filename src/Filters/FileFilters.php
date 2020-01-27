<?php

namespace Hamidrezaniazi\Upolo\Filters;

class FileFilters extends Filters
{
    /**
     * Registered filters to operate upon.
     *
     * @var array
     */
    protected $filters = ['owner_type', 'owner_id', 'creator_id'];

    /**
     * @param string $ownerType
     * @return mixed
     */
    protected function owner_type(string $ownerType)
    {
        return $this->builder->whereOwnerTypeIs($ownerType);
    }

    /**
     * @param int $ownerId
     * @return mixed
     */
    protected function owner_id(int $ownerId)
    {
        return $this->builder->whereOwnerIdIs($ownerId);
    }

    /**
     * @param int $creatorId
     * @return mixed
     */
    protected function creator_id(int $creatorId)
    {
        return $this->builder->whereCreatorIdIs($creatorId);
    }
}
