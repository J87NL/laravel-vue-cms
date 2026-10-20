# Navigation

The package does not render a sidebar. It adds its menu items to a config key, and your app builds its sidebar
from that key.

## Registered items

When the service provider registers, it pushes five items onto the config key named by
[`vue-cms.navigation_config_key`](configuration.md#navigation), default `j87nl.navigation`:

| `key` | `label` | `route` | `icon` | `permission` | `order` |
|---|---|---|---|---|---|
| `cms.pages` | `vue-cms::navigation.pages` | `cms.pages.index` | `FileText` | `cms.pages.edit` | `100` |
| `cms.media` | `vue-cms::navigation.media` | `cms.media.index` | `Image` | `cms.media` | `110` |
| `cms.menus` | `vue-cms::navigation.menus` | `cms.menus.index` | `Menu` | `cms.menus` | `120` |
| `cms.redirects` | `vue-cms::navigation.redirects` | `cms.redirects.index` | `Signpost` | `cms.redirects` | `130` |
| `cms.settings` | `vue-cms::navigation.settings` | `cms.settings.edit` | `Settings2` | `cms.settings` | `140` |

- `label` is a translation key.
- `route` is a route name.
- `icon` is the name of a Lucide icon.
- `permission` is the permission the user needs to see the item.
- `order` sorts the items between your own.

An item whose `key` is already present in the config key is skipped. To change an item, put your own version with
the same `key` in your config file:

```php
// config/j87nl.php
return [
    'navigation' => [
        [
            'key' => 'cms.media',
            'label' => 'Images',
            'route' => 'cms.media.index',
            'icon' => 'Image',
            'permission' => 'cms.media',
            'order' => 50,
        ],
    ],
];
```

## Building the sidebar

Read the items, keep those the user may see and sort them:

```php
$items = collect(config('j87nl.navigation', []))
    ->filter(fn (array $item): bool => $request->user()?->can($item['permission']) ?? false)
    ->sortBy('order')
    ->map(fn (array $item): array => [
        'label' => __($item['label']),
        'href' => route($item['route']),
        'icon' => $item['icon'],
    ])
    ->values();
```

Share the result with Inertia, for example from your `HandleInertiaRequests` middleware.
