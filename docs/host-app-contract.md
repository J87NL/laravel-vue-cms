# Host app contract

The Vue pages run inside your application's layout. They expect a few things from the host app.

## Confirm dialog

The pages never call `window.confirm`. They ask for confirmation through a function your layout provides under
the injection key `confirm`. It takes the message and resolves to `true` when the user agrees:

```vue
<script setup lang="ts">
import { provide } from 'vue';

provide('confirm', (message: string): Promise<boolean> => openConfirmDialog(message));
</script>
```

`openConfirmDialog` stands for your own dialog. The pages use it to delete a page, and pass it to every
TableBuilder table for row actions with a confirmation, such as deleting a block or restoring a revision.

Without a provided function every confirmation resolves to `false`, so destructive actions do nothing.

## Page padding

Every page wraps its content in `p-4`. When you render a page inside a layout that pads its content itself, such as
a settings layout with its own menu, provide `false` under the injection key `pagePadding` in that layout and the
page drops its own padding:

```ts
provide('pagePadding', false);
```

Without a provider the pages keep their padding. `usePagePadding()` from `@cms/composables/usePagePadding` reads the
key, which it exports as `PAGE_PADDING`.

## Breadcrumbs

Every page sets a `breadcrumbs` layout prop with `setLayoutProps()` from `@inertiajs/vue3`. The value is a list of
`{ title: string, href: string }`. Read it in your admin layout and render the trail.

```vue
<script setup lang="ts">
import type { CmsBreadcrumb } from '@cms/types';

defineProps<{
    breadcrumbs?: CmsBreadcrumb[];
}>();
</script>
```

## Permission helper

The pages call a global `$can(permission)` helper to hide buttons the user may not use, for example
`$can('cms.pages.publish')`. Register it on the Vue app yourself, based on the permissions you share with
Inertia. The server checks every permission again, so the helper only affects what is shown.

## Translations

All strings use `vue-cms::` keys and the `$t()` and `trans()` functions of `laravel-vue-i18n`. See
[Translations](translations.md).

## Toasts

Success messages arrive as Inertia flash data. Your layout shows them. See [Toasts](toasts.md).

## Validation errors

Some actions fail with a validation error on a key that is not a form field, such as `page`, `media` or
`import`. The pages read those keys from the `errors` page prop and show them above the content. Nothing is
needed from the host app for this.
