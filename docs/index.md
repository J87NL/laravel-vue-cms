# Laravel Vue CMS

An Inertia and Vue admin for `j87nl/laravel-cms`. It gives editors screens for pages, blocks, media, menus,
redirects and site settings.

The package holds no content logic of its own. Models, actions, validation rules and block types come from
`j87nl/laravel-cms`. This package adds the controllers, the forms and tables (built on TranquilTools FormBuilder
and TableBuilder), the routes and the Vue pages.

## How it works

1. The service provider registers the admin routes under `admin/cms`, behind `web` and `auth`.
2. Every route checks one of the CMS permissions, such as `cms.pages.edit` or `cms.media`.
3. A controller renders an Inertia page named `cms/...`, for example `cms/pages/Index`.
4. Your Inertia resolver loads names that start with `cms/` from the package's `resources/js/pages` folder.
5. The Vue pages render the FormBuilder forms and TableBuilder tables they receive as props.

## Quick example

Install the package and publish the config:

```bash
composer require j87nl/laravel-vue-cms
php artisan vendor:publish --tag=vue-cms-config
```

Then add the Vite aliases and let your Inertia resolver load the `cms/` pages from the package, as
[Installation](installation.md) describes.

Give a role the permissions it needs, then open `/admin/cms`. It redirects to the page tree at
`/admin/cms/pages`.

## Next steps

- [Installation](installation.md) - set up Composer, Vite, TypeScript and Tailwind
- [Host app contract](host-app-contract.md) - what your layout and Vue app must provide
- [Pages](screens/pages.md) - the page tree and the page editor
- [Permissions](permissions.md) - which permission opens which screen
- [Configuration](configuration.md) - every config key
