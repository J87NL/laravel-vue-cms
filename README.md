# j87nl/laravel-vue-cms

Inertia and Vue admin for [`j87nl/laravel-cms`](https://github.com/J87NL/laravel-cms): pages, blocks, media, menus, redirects and site settings, built on TranquilTools CrudBuilder, FormBuilder and TableBuilder.

The Vue pages stay inside the package. The app's Inertia resolver loads them from the package, and an app overrides a single page through the configurable page names in `config/vue-cms.php`.

## Requirements

- PHP 8.5, Laravel 13 and Inertia 3 with Vue 3
- `j87nl/laravel-cms`
- `tranquil-tools/laravel-vue-form-builder` and `tranquil-tools/laravel-vue-table-builder` (installed as dependencies)
- `laravel-vue-i18n` and a `$can(permission)` helper on the Vue app

## Installation

```bash
composer require j87nl/laravel-vue-cms
php artisan vendor:publish --tag=vue-cms-config
```

The admin routes live under `vue-cms.admin.prefix` (default `admin/cms`) behind `vue-cms.admin.middleware` (default `web` and `auth`), and every screen checks one of the CMS permissions.

### Vite and the page resolver

Add the aliases and load the package pages next to your own:

```ts
// vite.config.ts
resolve: {
    alias: {
        '@cms': fileURLToPath(new URL('./vendor/j87nl/laravel-vue-cms/resources/js', import.meta.url)),
        '@form-builder': fileURLToPath(new URL('./vendor/tranquil-tools/laravel-vue-form-builder/resources/js', import.meta.url)),
        '@table-builder': fileURLToPath(new URL('./vendor/tranquil-tools/laravel-vue-table-builder/resources/js', import.meta.url)),
    },
},
```

```ts
// resources/js/app.ts
const appPages = import.meta.glob<DefineComponent>('./pages/**/*.vue');
const cmsPages = import.meta.glob<DefineComponent>('@cms/pages/**/*.vue');

resolve: async (name) => {
    const page = name.startsWith('cms/')
        ? Object.entries(cmsPages).find(([path]) => path.endsWith(`/pages/${name.slice(4)}.vue`))?.[1]
        : appPages[`./pages/${name}.vue`];

    return (await page!()).default;
},
```

Add `"@cms/*": ["./vendor/j87nl/laravel-vue-cms/resources/js/*"]` to the `paths` in `tsconfig.json`, and add the package's `.vue` files to Tailwind's sources so their classes are generated.

### Navigation and permissions

The package pushes its menu items into the config key named by `vue-cms.navigation_config_key` (default `j87nl.navigation`). Each item has a `key`, a translation `label`, a `route`, a Lucide `icon` name (`FileText`, `Image`, `Menu`, `Signpost`, `Settings2`), a `permission` and an `order`. The app builds its sidebar from that key.

Map the CMS permissions to your roles: `cms.pages.edit`, `cms.pages.publish`, `cms.menus`, `cms.media`, `cms.redirects` and `cms.settings`. Publishing, taking offline, planning and changing a published page all need `cms.pages.publish`.

### Toasts

Success messages are flashed with `Inertia::flash()` under `vue-cms.toast_flash_key` (default `toast`) as `{ type, message }`.

## Testing

```bash
composer update
vendor/bin/pest
```

## License

MIT
