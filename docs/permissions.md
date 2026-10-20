# Permissions

The admin uses the six permissions of the `J87NL\Cms\CmsPermission` enum from laravel-cms:

| Permission | Enum case | Opens |
|---|---|---|
| `cms.pages.edit` | `PagesEdit` | The page tree, the page editor, blocks and revisions |
| `cms.pages.publish` | `PagesPublish` | Publishing, taking offline, the home page, planning, and changing or deleting a published page |
| `cms.menus` | `Menus` | The menus screen |
| `cms.media` | `Media` | The media screen |
| `cms.redirects` | `Redirects` | The redirects screen |
| `cms.settings` | `Settings` | The site settings |

The routes check them with Laravel's `can:` middleware, so `$user->can('cms.media')` must answer for each of
them. Map them to your roles with gates or a permission package. laravel-cms adds the permission names to the config
key named by its `cms.permissions_config_key` setting, default `j87nl.permissions`. See the laravel-cms
documentation.

`cms.pages.publish` adds to `cms.pages.edit`. Every publication route needs both.

Guests are stopped by the `auth` middleware. A logged-in user without the permission gets `403`.

## Changing a published page

The service provider defines one extra gate ability, `cms.pages.write`, through
`J87NL\VueCms\Support\PageWriteAccess`:

```php
use J87NL\VueCms\Support\PageWriteAccess;

PageWriteAccess::ABILITY; // 'cms.pages.write'
```

A user may write to a page when they have `cms.pages.edit` and either:

- the page is not published, or
- they also have `cms.pages.publish`.

This ability guards every change to the content of a page: saving the page form, moving the page, adding,
editing, hiding, deleting and reordering blocks, and restoring a revision.

`PageWriteAccess::blockedReason($user, $page)` returns the reason a user may not write, or `null`. The page tree
and the page editor use it to disable the controls up front, with the translated reason
`vue-cms::pages.publication.write_requires_publish`.

You can use the ability in your own code:

```php
Gate::authorize('cms.pages.write', $page);

$request->user()->can('cms.pages.write', $page);
```

## Deleting a page

Deleting needs `cms.pages.edit`. Deleting a published page also needs `cms.pages.publish`; without it the
request answers `403`.
