# Installation

## Requirements

- PHP 8.5
- Laravel 13
- Inertia 3 with Vue 3
- `j87nl/laravel-cms` 0.2, installed and migrated as its own documentation describes
- `laravel-vue-i18n` in the Vue app
- A global `$can(permission)` helper in the Vue app, see [Host app contract](host-app-contract.md)

## 1. Install via Composer

```bash
composer require j87nl/laravel-vue-cms
```

This pulls in `j87nl/laravel-cms`, `tranquil-tools/laravel-vue-form-builder` and
`tranquil-tools/laravel-vue-table-builder`. The service provider `J87NL\VueCms\VueCmsServiceProvider` is
auto-discovered.

The FormBuilder and TableBuilder packages ship their own `.vue` files and have their own front-end
dependencies. Follow their installation guides for the npm packages they need.

## 2. Publish the config (optional)

```bash
php artisan vendor:publish --tag=vue-cms-config
```

This creates `config/vue-cms.php`. See [Configuration](configuration.md) for every key.

## 3. Add the Vite aliases

The Vue pages import from three aliases. Add them to `vite.config.ts`:

```ts
import { fileURLToPath, URL } from 'node:url';

export default defineConfig({
    // ...
    resolve: {
        alias: {
            '@cms': fileURLToPath(new URL('./vendor/j87nl/laravel-vue-cms/resources/js', import.meta.url)),
            '@form-builder': fileURLToPath(new URL('./vendor/tranquil-tools/laravel-vue-form-builder/resources/js', import.meta.url)),
            '@table-builder': fileURLToPath(new URL('./vendor/tranquil-tools/laravel-vue-table-builder/resources/js', import.meta.url)),
        },
    },
});
```

## 4. Load the package pages

The controllers render page names that start with `cms/`. Resolve those from the package and every other name
from your own pages:

```ts
// resources/js/app.ts
const appPages = import.meta.glob<DefineComponent>('./pages/**/*.vue');
const cmsPages = import.meta.glob<DefineComponent>('@cms/pages/**/*.vue');

createInertiaApp({
    resolve: async (name) => {
        const page = name.startsWith('cms/')
            ? Object.entries(cmsPages).find(([path]) => path.endsWith(`/pages/${name.slice(4)}.vue`))?.[1]
            : appPages[`./pages/${name}.vue`];

        return (await page!()).default;
    },
    // ...
});
```

The page names are configurable, so you can replace a single screen with your own page. See
[Vue pages](frontend/vue-pages.md).

## 5. TypeScript

Map the aliases in `tsconfig.json`. The package pages import from all three:

```json
{
    "compilerOptions": {
        "paths": {
            "@cms/*": ["./vendor/j87nl/laravel-vue-cms/resources/js/*"],
            "@form-builder/*": ["./vendor/tranquil-tools/laravel-vue-form-builder/resources/js/*"],
            "@table-builder/*": ["./vendor/tranquil-tools/laravel-vue-table-builder/resources/js/*"]
        }
    }
}
```

## 6. Tailwind CSS

The pages use Tailwind classes. Add the package's `.vue` files to the sources in `resources/css/app.css`:

```css
@source '../../vendor/j87nl/laravel-vue-cms/resources/js/**/*.vue';
```

## 7. Translations

The pages translate with `vue-cms::` keys through `laravel-vue-i18n`. Make the package's `lang` folder visible to
the i18n Vite plugin, for example with a symlink:

```bash
mkdir -p lang/vendor
ln -s ../../vendor/j87nl/laravel-vue-cms/lang lang/vendor/vue-cms
```

See [Translations](translations.md).

## 8. TableBuilder routes

Reordering blocks and menu items posts to the TableBuilder package routes. Run those routes with a session and a
logged-in user, so the tables can check the user's permissions:

```php
// config/table-builder.php
'routes' => [
    'middleware' => ['web', 'auth'],
],
```

The TableBuilder documentation describes these routes and their signed URLs.

## 9. Permissions and layout

- Make the CMS permissions resolvable through `$user->can()`. See [Permissions](permissions.md).
- Provide a confirm function, read the `breadcrumbs` layout prop and show the toasts. See
  [Host app contract](host-app-contract.md).
- Build your sidebar from the registered menu items. See [Navigation](navigation.md).

## 10. Build assets

```bash
npm run build
```
