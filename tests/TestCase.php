<?php

namespace Hamidrezaniazi\Upolo\Tests;

use Hamidrezaniazi\Upolo\Tests\Migrations\CreateMockModelsTable;
use Hamidrezaniazi\Upolo\Tests\Migrations\CreateUpoloUsersTable;
use Hamidrezaniazi\Upolo\Tests\Models\User;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withFactories(__DIR__.'/factories');
        $this->withFactories(__DIR__ . '/../database/factories');

    }

    protected function getEnvironmentSetUp($app)
    {
        include_once __DIR__.'/../database/migrations/create_files_table.php.stub';

        (new \CreateFilesTable())->up();
        (new CreateUpoloUsersTable())->up();
        (new CreateMockModelsTable())->up();

        $app['config']->set('auth.providers.users.model', User::class);
    }
}
