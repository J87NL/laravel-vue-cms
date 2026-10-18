<?php

declare(strict_types=1);

namespace J87NL\VueCms\Tests;

use Composer\InstalledVersions;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Inertia\ServiceProvider as InertiaServiceProvider;
use J87NL\Cms\CmsServiceProvider;
use J87NL\VueCms\VueCmsServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Spatie\MediaLibrary\MediaLibraryServiceProvider;
use TranquilTools\FormBuilder\FormBuilderServiceProvider;
use TranquilTools\TableBuilder\TableBuilderServiceProvider;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

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
            FormBuilderServiceProvider::class,
            TableBuilderServiceProvider::class,
            MediaLibraryServiceProvider::class,
            CmsServiceProvider::class,
            VueCmsServiceProvider::class,
        ];
    }

    /**
     * @param  Application  $app
     */
    protected function defineEnvironment($app): void
    {
        $app['config']->set('media-library.queue_connection_name', 'sync');
        $app['config']->set('queue.default', 'sync');
        $app['config']->set('database.connections.testing.foreign_key_constraints', true);
        $app['config']->set('table-builder.routes.middleware', [
            'web',
            'auth',
        ]);
    }

    protected function defineDatabaseMigrations(): void
    {
        $mediaLibrary = (string) InstalledVersions::getInstallPath('spatie/laravel-medialibrary');

        $migration = include sprintf('%s/database/migrations/create_media_table.php.stub', $mediaLibrary);

        $migration->up();

        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
    }

    /**
     * @param  list<string>  $permissions
     */
    protected function actingWithPermissions(array $permissions): static
    {
        foreach ($permissions as $permission) {
            Gate::define($permission, fn (): bool => true);
        }

        $user = new User;
        $user->forceFill([
            'id' => 1,
        ]);

        return $this->actingAs($user);
    }

    /**
     * @return array<string, string>
     */
    protected function inertiaHeaders(): array
    {
        return [
            'X-Inertia' => 'true',
        ];
    }
}
