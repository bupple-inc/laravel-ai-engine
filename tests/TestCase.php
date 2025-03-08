<?php

namespace Bupple\Engine\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use BuppleEngine\Providers\BuppleEngineServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app): array
    {
        return [
            BuppleEngineServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        // Perform any environment setup
    }
}
