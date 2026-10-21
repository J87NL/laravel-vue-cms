# Configuration

Publish the config file to change the defaults:

```bash
php artisan vendor:publish --tag=vue-cms-config
```

This creates `config/vue-cms.php`. The package reads no environment variables.

This file only holds the settings of the admin. The content model, the public website and the MCP tools are set in
`config/cms.php` of laravel-cms, see its
[configuration](https://github.com/J87NL/laravel-cms/blob/main/docs/configuration.md).

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

## Settings read from laravel-cms

A few screens read the config of `j87nl/laravel-cms` (`config/cms.php`). Change these keys there:

| Key | Used for |
|---|---|
| [`cms.menus`](https://github.com/J87NL/laravel-cms/blob/main/docs/configuration.md#blocks-and-menus) | The menus shown on the [Menus](screens/menus.md) screen |
| [`cms.media.max_upload_kilobytes`](https://github.com/J87NL/laravel-cms/blob/main/docs/configuration.md#media) | The maximum upload size on the [Media](screens/media.md) screen. Falls back to `10240` when missing. The `UploadMedia` action of the core checks it too. |
| [`cms.schema_types`](https://github.com/J87NL/laravel-cms/blob/main/docs/configuration.md#schema-types) | The options of the "Page type" select in the page editor. A page may only be saved with one of these values, or with no value. The value is both the option value and its label. Up to version 0.2.0 this list was `vue-cms.schema_types`; that key is no longer read. |
| [`cms.revisions.keep`](https://github.com/J87NL/laravel-cms/blob/main/docs/revisions.md#pruning) | The number of revisions the page editor says are kept |
| [`cms.preview.minutes`](https://github.com/J87NL/laravel-cms/blob/main/docs/configuration.md#preview-and-scheduling) | How long the preview link on the page editor stays valid, see [Publication](screens/publication.md#preview) |
| [`cms.blocks.disabled`](https://github.com/J87NL/laravel-cms/blob/main/docs/blocks.md#disabling-block-types) | Disabled block types get no add button, and their create, store, edit and update routes answer `404`. Existing blocks of a disabled type stay in the [Blocks](screens/blocks.md) list with the raw type key and no edit link. |
| [`cms.rich_text.*`](https://github.com/J87NL/laravel-cms/blob/main/docs/configuration.md#rich-text) | The editor key and options of the rich text fields in the block forms, see [Installation](installation.md) |
