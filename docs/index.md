# Laravel Vue CMS

An Inertia and Vue admin for `j87nl/laravel-cms`. It gives editors screens for pages, blocks, media, menus,
redirects and site settings.

## How the packages fit together

The CMS is split into two packages. The admin requires the core; the core also works on its own:

| Package | What it does |
|---|---|
| `j87nl/laravel-vue-cms` (this package) | The admin: routes, controllers, FormBuilder forms, TableBuilder tables and Vue pages for editors |
| [`j87nl/laravel-cms`](https://github.com/J87NL/laravel-cms) | The core: models, actions, validation rules, block types, permissions, public rendering, SEO, sitemap, page cache and MCP tools |

The boundary rule: **the core owns the data, the rules, the actions and the public website; this package owns the
admin screens and saves through the core's actions.**

This package holds no content logic of its own. When a screen saves something, the core action decides what is
valid and takes care of revisions, redirects and the page cache. The core has no action for four changes, so the
admin writes to the core's models directly:

- changing the alt text, caption or focal point of an image,
- deleting a redirect,
- deleting a menu item,
- reordering menu items.

See the [actions](https://github.com/J87NL/laravel-cms/blob/main/docs/actions.md) of laravel-cms.

Which package to install:

- You want an admin for editors: install this package. It requires `j87nl/laravel-cms`, so Composer installs
  both. You still follow a few installation steps of the core, see [Installation](installation.md).
- You only need the public website, or you edit content in code or through the MCP tools: install
  `j87nl/laravel-cms` alone.

These docs describe the admin. For how pages, blocks, media, menus, redirects and revisions behave, read the
[laravel-cms documentation](https://github.com/J87NL/laravel-cms/blob/main/docs/index.md).

## How it works

1. The service provider registers the admin routes under `admin/cms`, behind `web` and `auth`.
2. Every route checks one of the CMS permissions, such as `cms.pages.edit` or `cms.media`.
3. A controller renders an Inertia page named `cms/...`, for example `cms/pages/Index`.
4. Your Inertia resolver loads names that start with `cms/` from the package's `resources/js/pages` folder.
5. The Vue pages render the FormBuilder forms and TableBuilder tables they receive as props.
6. A save calls an action of laravel-cms, such as `SavePage` or `UploadMedia`.

## Quick example

Install the package and publish the config:

```bash
composer require j87nl/laravel-vue-cms
php artisan vendor:publish --tag=vue-cms-config
```

Then follow the core steps and add the Vite aliases and the Inertia resolver, as
[Installation](installation.md) describes.

Give a role the permissions it needs, then open `/admin/cms`. It redirects to the page tree at
`/admin/cms/pages`.

## Next steps

- [Installation](installation.md) - set up the core, Composer, Vite, TypeScript and Tailwind
- [Host app contract](host-app-contract.md) - what your layout and Vue app must provide
- [Pages](screens/pages.md) - the page tree and the page editor
- [Permissions](permissions.md) - which permission opens which screen
- [Configuration](configuration.md) - every config key
- [laravel-cms documentation](https://github.com/J87NL/laravel-cms/blob/main/docs/index.md) - the content model,
  the public website and the MCP tools
