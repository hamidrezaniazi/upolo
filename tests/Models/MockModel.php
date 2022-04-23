<?php

namespace Hamidrezaniazi\Upolo\Tests\Models;

use Hamidrezaniazi\Upolo\Contracts\HasFileInterface;
use Hamidrezaniazi\Upolo\Database\Factories\MockModelFactory;
use Hamidrezaniazi\Upolo\Traits\HasFileTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MockModel extends Model implements HasFileInterface
{
    use HasFactory;
    use HasFileTrait;

    protected static function newFactory()
    {
        return new MockModelFactory();
    }
}
