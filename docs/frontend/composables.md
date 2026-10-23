# Composables and types

The pages share four composables, a few types and a set of class names. You can import them in your own pages
through the `@cms` alias, for example when you [replace a page](vue-pages.md#replacing-a-page).

## useBreadcrumbs

```ts
import { useBreadcrumbs } from '@cms/composables/useBreadcrumbs';

const props = defineProps<{ breadcrumbs: CmsBreadcrumb[] }>();

useBreadcrumbs(() => props.breadcrumbs);
```

Sets the `breadcrumbs` layout prop with `setLayoutProps()` and keeps it up to date in a `watchEffect`.

## useConfirm

```ts
import { useConfirm } from '@cms/composables/useConfirm';

const confirm = useConfirm();

if (await confirm(trans('vue-cms::pages.actions.delete_confirm'))) {
    // ...
}
```

Returns the function provided under the injection key `confirm`. Without one it returns a function that always
resolves to `false`. See [Host app contract](../host-app-contract.md#confirm-dialog).

The type of the function is exported as `CmsConfirm`:

```ts
export type CmsConfirm = (message: string) => Promise<boolean>;
```

## useErrors

```ts
import { useErrors } from '@cms/composables/useErrors';

const errors = useErrors(['import']);
```

Returns a computed list of the messages in the `errors` page prop for the given keys. Keys without a message are
left out.

## usePagePadding

```vue
<script setup lang="ts">
import { usePagePadding } from '@cms/composables/usePagePadding';

const padded = usePagePadding();
</script>

<template>
    <div class="flex flex-col gap-6" :class="{ 'p-4': padded }">
        <!-- ... -->
    </div>
</template>
```

Returns the value provided under the injection key `pagePadding` (exported as `PAGE_PADDING`), or `true` without
one. See [Host app contract](../host-app-contract.md#page-padding).

## Types

`@cms/types` exports:

```ts
export type CmsBreadcrumb = {
    title: string;
    href: string;
};

export type CmsPageNode = {
    id: number;
    label: string;
    path: string;
    status: string;
    statusLabel: string;
    isHome: boolean;
    editUrl: string;
    moveUrl: string;
    moveBlockedReason: string | null;
    children: CmsPageNode[];
};

export type CmsTreeDrop = {
    dragged: { id: string | number };
    target: { id: string | number } | null;
    position: 'before' | 'after' | 'inside';
};
```

`CmsRevisionDiff`, `CmsBlockDiff`, `CmsValueDiff` and `CmsWordSegment` describe the comparison of a pending revision
with the live page, see [Vue pages](vue-pages.md#cmspagesedit).

## Class names

`@cms/ui` exports `ui`, an object of Tailwind class strings the pages use: `button`, `primaryButton`,
`dangerButton`, `panel`, `heading`, `title`, `toolbar`, `muted`, `badge`, `publishedBadge`, `error` and
`notice`. They include `dark:` variants.

```vue
<script setup lang="ts">
import { ui } from '@cms/ui';
</script>

<template>
    <section :class="ui.panel">
        <h2 :class="ui.heading">Title</h2>
    </section>
</template>
```
