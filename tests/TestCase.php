<?php

declare(strict_types=1);

namespace J87NL\VueCms\Tests;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Http;
use Inertia\ServiceProvider as InertiaServiceProvider;
use J87NL\Cms\CmsServiceProvider;
use J87NL\VueCms\VueCmsServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Http::preventStrayRequests();
    }

    /**
     * @param  Application  $app
     * @return list<class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            InertiaServiceProvider::class,
            CmsServiceProvider::class,
            VueCmsServiceProvider::class,
        ];
    }
}
