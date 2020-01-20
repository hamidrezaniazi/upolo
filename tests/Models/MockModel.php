<?php

namespace Hamidrezaniazi\Upolo\Tests\Models;

use Hamidrezaniazi\Upolo\Contracts\HasFileInterface;
use Hamidrezaniazi\Upolo\Traits\HasFileTrait;
use Illuminate\Database\Eloquent\Model;

class MockModel extends Model implements HasFileInterface
{
    use HasFileTrait;
}
