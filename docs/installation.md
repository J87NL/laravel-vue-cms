# Installation

## Requirements

- PHP 8.5
- Laravel 13
- Inertia 3 with Vue 3
- `j87nl/laravel-cms` 0.2.2 or later within 0.2 (installed for you, see step 1)
- `laravel-vue-i18n` in the Vue app
- A global `$can(permission)` helper in the Vue app, see [Host app contract](host-app-contract.md)

## 1. Install via Composer

```bash
composer require j87nl/laravel-vue-cms
```

This package is the admin. It requires and installs `j87nl/laravel-cms`, the core that holds the models, actions,
block types and public website, so this one `require` installs both. It also pulls in
`tranquil-tools/laravel-vue-form-builder` and `tranquil-tools/laravel-vue-table-builder`. The service provider
`J87NL\VueCms\VueCmsServiceProvider` is auto-discovered.

The FormBuilder and TableBuilder packages ship their own `.vue` files and have their own front-end
dependencies. Follow their installation guides for the npm packages they need.

## 2. Set up laravel-cms

Composer installs the core, but it does not set it up. Follow the
[laravel-cms installation guide](https://github.com/J87NL/laravel-cms/blob/main/docs/installation.md) for these
steps first. The admin does not work without them:

- **The media table and the migrations.** Every screen reads the `cms_*` tables, and the media screen needs the
  `media` table of `spatie/laravel-medialibrary`.
- **`php artisan storage:link`.** The media list and the media edit screen show images from the `cms.media.disk`
  disk, `public` by default.
- **A gate per CMS permission.** Every admin route checks a permission with the `can:` middleware, and the core
  defines no gates. See [Permissions](permissions.md).
- **The scheduler.** The planning form only stores the moments. The `cms:publish-scheduled` command of the core
  carries them out.

These core steps matter as well:

- **A queue worker.** The core creates WebP versions of uploads on the queue. Until they exist, the original
  file is used.
- **The layout.** The preview link renders the page in the core's `cms.layout`, like the public website.

The other core steps, such as removing `public/robots.txt` and hiding the contact form honeypot, are for the
public website.

## 3. Publish the config (optional)

```bash
php artisan vendor:publish --tag=vue-cms-config
```

This creates `config/vue-cms.php`. See [Configuration](configuration.md) for every key.

## 4. Add the Vite aliases

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

## 5. Load the package pages

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

## 6. TypeScript

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

## 7. Tailwind CSS

The pages use Tailwind classes. Add the package's `.vue` files to the sources in `resources/css/app.css`:

```css
@source '../../vendor/j87nl/laravel-vue-cms/resources/js/**/*.vue';
```

## 8. Translations

The pages translate with `vue-cms::` keys through `laravel-vue-i18n`. Make the package's `lang` folder visible to
the i18n Vite plugin, for example with a symlink:

```bash
mkdir -p lang/vendor
ln -s ../../vendor/j87nl/laravel-vue-cms/lang lang/vendor/vue-cms
```

See [Translations](translations.md).

## 9. TableBuilder routes

Reordering blocks and menu items posts to the TableBuilder package routes. Run those routes with a session and a
logged-in user, so the tables can check the user's permissions:

```php
// config/table-builder.php
'routes' => [
    'middleware' => ['web', 'auth'],
],
```

The TableBuilder documentation describes these routes and their signed URLs.

## 10. Rich text editor

The `rich_text` and `text_image` blocks of laravel-cms use a FormBuilder `Wysiwyg` field with the editor key from
the core's `cms.rich_text.editor` config, `hugerte` by default. FormBuilder imports no editor by itself, so register
one under that key in your Vue app:

```ts
import { registerWysiwygEditor } from '@form-builder/wysiwyg/registry';
import { defineAsyncComponent } from 'vue';

registerWysiwygEditor('hugerte', defineAsyncComponent(() => import('@form-builder/wysiwyg/HugeRteEditor.vue')));
```

The FormBuilder documentation on WYSIWYG adapters describes the npm packages and plugins the editor needs. The
editor options come from the core, see
[its rich text config](https://github.com/J87NL/laravel-cms/blob/main/docs/configuration.md#rich-text).

## 11. Permissions and layout

- Define the CMS permission gates as described in step 2. See [Permissions](permissions.md).
- Provide a confirm function, read the `breadcrumbs` layout prop and show the toasts. See
  [Host app contract](host-app-contract.md).
- Build your sidebar from the registered menu items. See [Navigation](navigation.md).

## 12. Build assets

```bash
npm run build
```
