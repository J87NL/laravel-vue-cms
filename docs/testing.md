# Testing

The package is tested with Pest on Orchestra Testbench, against an in-memory SQLite database.

```bash
composer update
vendor/bin/pest
```

## Test setup

`tests/TestCase.php` boots the service providers of Inertia, FormBuilder, TableBuilder, Spatie Media Library,
laravel-cms and this package. It runs the Media Library migration and creates a small `users` table.

It also:

- runs the Media Library queue and the default queue on `sync`,
- turns on foreign key constraints for SQLite,
- sets `table-builder.routes.middleware` to `web` and `auth`,
- prevents stray HTTP requests with `Http::preventStrayRequests()`.

Two helpers make feature tests short:

| Helper | Does |
|---|---|
| `actingWithPermissions(array $permissions)` | Defines a gate that allows each given permission and logs in a user with id `1` |
| `inertiaHeaders()` | Returns the `X-Inertia: true` header, so a response comes back as Inertia JSON |

`tests/Pest.php` adds two functions:

| Function | Does |
|---|---|
| `makePage(array $attributes = [], PageStatus $status = PageStatus::Draft): Page` | Saves a page titled "Page" through the `SavePage` action of laravel-cms, merged with `$attributes`, and gives it `$status` |
| `redirectTargets(): array` | Returns all redirects as `from_path => to`, sorted by `from_path` |

```php
<?php

declare(strict_types=1);

use J87NL\Cms\CmsPermission;

test('the configured page component is rendered', function (): void {
    config()->set('vue-cms.pages.pages_index', 'admin/CustomPages');

    $this->actingWithPermissions([CmsPermission::PagesEdit->value])
        ->get(route('cms.pages.index'), $this->inertiaHeaders())
        ->assertOk()
        ->assertJsonPath('component', 'admin/CustomPages');
});
```

## Testing your app

In your own application, test the CMS screens the same way: give the user the permissions through your role
setup, then call the named routes. The Vue pages are not covered by the package tests.
