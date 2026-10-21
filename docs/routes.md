# Routes

The package registers its routes from `routes/admin.php`. Every route:

- has the URL prefix [`vue-cms.admin.prefix`](configuration.md#admin-routes), default `admin/cms`,
- runs behind [`vue-cms.admin.middleware`](configuration.md#admin-routes), default `web` and `auth`,
- has a name starting with `cms.`,
- checks a permission with the `can:` middleware.

The URLs below leave out the prefix.

## Pages

Permission: `cms.pages.edit`.

| Method | URL | Name |
|---|---|---|
| `GET` | `/` | `cms.dashboard` (redirects to `pages`) |
| `GET` | `pages` | `cms.pages.index` |
| `GET` | `pages/create` | `cms.pages.create` |
| `POST` | `pages` | `cms.pages.store` |
| `GET` | `pages/{page}/edit` | `cms.pages.edit` |
| `PUT` | `pages/{page}` | `cms.pages.update` |
| `DELETE` | `pages/{page}` | `cms.pages.destroy` |
| `POST` | `pages/{page}/move` | `cms.pages.move` |
| `GET` | `pages/{page}/blocks/create/{type}` | `cms.blocks.create` |
| `POST` | `pages/{page}/blocks/{type}` | `cms.blocks.store` |
| `GET` | `pages/{page}/blocks/{block}/edit` | `cms.blocks.edit` |
| `PUT` | `pages/{page}/blocks/{block}` | `cms.blocks.update` |
| `DELETE` | `pages/{page}/blocks/{block}` | `cms.blocks.destroy` |
| `POST` | `pages/{page}/blocks/{block}/visibility` | `cms.blocks.toggle` |
| `POST` | `pages/{page}/revisions/{revision}/restore` | `cms.revisions.restore` |

## Publication

Permissions: `cms.pages.edit` and `cms.pages.publish`.

| Method | URL | Name |
|---|---|---|
| `POST` | `pages/{page}/publish` | `cms.pages.publish` |
| `POST` | `pages/{page}/unpublish` | `cms.pages.unpublish` |
| `POST` | `pages/{page}/home` | `cms.pages.home` |
| `POST` | `pages/{page}/schedule` | `cms.pages.schedule` |

## Media

Permission: `cms.media`.

| Method | URL | Name |
|---|---|---|
| `GET` | `media` | `cms.media.index` |
| `GET` | `media/create` | `cms.media.create` |
| `POST` | `media` | `cms.media.store` |
| `GET` | `media/{asset}/edit` | `cms.media.edit` |
| `PUT` | `media/{asset}` | `cms.media.update` |
| `POST` | `media/{asset}/replace` | `cms.media.replace` |
| `DELETE` | `media/{asset}` | `cms.media.destroy` |

## Menus

Permission: `cms.menus`. The `{menu}` parameter is the menu key, such as `main`.

| Method | URL | Name |
|---|---|---|
| `GET` | `menus` | `cms.menus.index` |
| `GET` | `menus/{menu}/items/create` | `cms.menu-items.create` |
| `POST` | `menus/{menu}/items` | `cms.menu-items.store` |
| `GET` | `menus/{menu}/items/{item}/edit` | `cms.menu-items.edit` |
| `PUT` | `menus/{menu}/items/{item}` | `cms.menu-items.update` |
| `DELETE` | `menus/{menu}/items/{item}` | `cms.menu-items.destroy` |

## Redirects

Permission: `cms.redirects`.

| Method | URL | Name |
|---|---|---|
| `GET` | `redirects` | `cms.redirects.index` |
| `GET` | `redirects/create` | `cms.redirects.create` |
| `POST` | `redirects` | `cms.redirects.store` |
| `GET` | `redirects/import` | `cms.redirects.import` |
| `POST` | `redirects/import` | `cms.redirects.import.store` |
| `GET` | `redirects/{redirect}/edit` | `cms.redirects.edit` |
| `PUT` | `redirects/{redirect}` | `cms.redirects.update` |
| `DELETE` | `redirects/{redirect}` | `cms.redirects.destroy` |

## Settings

Permission: `cms.settings`.

| Method | URL | Name |
|---|---|---|
| `GET` | `settings` | `cms.settings.edit` |
| `PUT` | `settings` | `cms.settings.update` |

## Responses

The `GET` routes render an Inertia page. The other routes redirect back to the list or the page editor and flash
a [toast](toasts.md).

Routes with `{page}` and `{block}` or `{revision}`, and routes with `{menu}` and `{item}`, use scoped bindings, so a
child of another parent answers `404`.

## Other routes

Reordering blocks and menu items uses the reorder route of TableBuilder, not a route of this package. See
[Security](security.md#tablebuilder-routes).
