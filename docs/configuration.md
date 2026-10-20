# Configuration

Publish the config file to change the defaults:

```bash
php artisan vendor:publish --tag=vue-cms-config
```

This creates `config/vue-cms.php`. The package reads no environment variables.

## Admin routes

```php
'admin' => [
    'prefix' => 'admin/cms',
    'middleware' => [
        'web',
        'auth',
    ],
],
```

| Key | Default | Description |
|---|---|---|
| `admin.prefix` | `'admin/cms'` | The URL prefix of every admin route. |
| `admin.middleware` | `['web', 'auth']` | The middleware around every admin route. The permission checks are added per route on top of it. |

The routes are registered when the application boots, so set these values in the config file, not at runtime.
See [Routes](routes.md).

## Page components

```php
'pages' => [
    'pages_index' => 'cms/pages/Index',
    'pages_edit' => 'cms/pages/Edit',
    'form' => 'cms/Form',
    'media_index' => 'cms/media/Index',
    'menus_index' => 'cms/menus/Index',
    'redirects_index' => 'cms/redirects/Index',
],
```

The Inertia component name each controller renders. Change a value to render your own page instead of the
package page. See [Vue pages](frontend/vue-pages.md).

| Key | Default | Rendered by |
|---|---|---|
| `pages.pages_index` | `'cms/pages/Index'` | The page tree |
| `pages.pages_edit` | `'cms/pages/Edit'` | The page editor |
| `pages.form` | `'cms/Form'` | Every single-form screen: new page, block, media, menu item, redirect, CSV import and site settings |
| `pages.media_index` | `'cms/media/Index'` | The media list |
| `pages.menus_index` | `'cms/menus/Index'` | The menus overview |
| `pages.redirects_index` | `'cms/redirects/Index'` | The redirects list |

## Navigation

```php
'navigation_config_key' => 'j87nl.navigation',
```

The config key the package pushes its menu items into. See [Navigation](navigation.md).

## Toasts

```php
'toast_flash_key' => 'toast',
```

The key the success messages are flashed under with `Inertia::flash()`. See [Toasts](toasts.md).

## Schema types

```php
'schema_types' => [
    'WebPage',
    'AboutPage',
    'ContactPage',
    'CollectionPage',
    'FAQPage',
],
```

The options of the "Page type" select in the page editor. A page may only be saved with one of these values, or
with no value. The value is used as both the option value and its label.

## Settings read from laravel-cms

A few screens read the config of `j87nl/laravel-cms` (`config/cms.php`):

| Key | Used for |
|---|---|
| `cms.menus` | The menus shown on the [Menus](screens/menus.md) screen |
| `cms.media.max_upload_kilobytes` | The maximum upload size on the [Media](screens/media.md) screen. Falls back to `10240` when missing. |
| `cms.preview.minutes` | How long the preview link on the page editor stays valid, see [Publication](screens/publication.md#preview) |
| `cms.blocks.disabled` | Disabled block types get no add button, and their create, store, edit and update routes answer `404`. Existing blocks of a disabled type stay in the [Blocks](screens/blocks.md) list with the raw type key and no edit link. |

See the laravel-cms documentation for those keys.
