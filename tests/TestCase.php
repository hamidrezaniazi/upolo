<?php

namespace Hamidrezaniazi\Upolo\Tests;

use Hamidrezaniazi\Upolo\Tests\Migrations\CreateUpoloUsersTable;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withFactories(__DIR__ . '/../database/factories');
    }

    protected function getEnvironmentSetUp($app)
    {
        (new CreateUpoloUsersTable())->up();
    }
}
