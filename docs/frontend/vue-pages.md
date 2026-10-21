# Vue pages

The package ships six Inertia pages in `resources/js/pages`. Your Inertia resolver loads them through the `@cms`
alias, see [Installation](../installation.md).

| Component | Config key | File |
|---|---|---|
| `cms/pages/Index` | `pages.pages_index` | `pages/pages/Index.vue` |
| `cms/pages/Edit` | `pages.pages_edit` | `pages/pages/Edit.vue` |
| `cms/Form` | `pages.form` | `pages/Form.vue` |
| `cms/media/Index` | `pages.media_index` | `pages/media/Index.vue` |
| `cms/menus/Index` | `pages.menus_index` | `pages/menus/Index.vue` |
| `cms/redirects/Index` | `pages.redirects_index` | `pages/redirects/Index.vue` |

## Replacing a page

Each controller renders the component name from `config('vue-cms.pages.*')`. Point a key at one of your own pages
to replace that screen:

```php
// config/vue-cms.php
'pages' => [
    // ...
    'pages_edit' => 'admin/cms/PageEditor',
],
```

The name no longer starts with `cms/`, so your resolver loads `resources/js/pages/admin/cms/PageEditor.vue`. Your
page receives the same props as the package page, listed below.

## Props

Every page receives `breadcrumbs`, a list of `{ title, href }`, and passes it to the layout. See
[Composables and types](composables.md#usebreadcrumbs).

### cms/Form

One page for every screen that shows a single form.

| Prop | Type | Description |
|---|---|---|
| `title` | `string` | The heading |
| `description` | `string \| null` | A line below the heading |
| `breadcrumbs` | `CmsBreadcrumb[]` | |
| `backUrl` | `string \| null` | The target of the back button. No button when empty. |
| `form` | `FormSchema` | The FormBuilder form |
| `preview` | `{ url: string \| null, alt: string, objectPosition: string } \| null` | An image above the form. Only the media edit screen sends it. |

### cms/pages/Index

| Prop | Type | Description |
|---|---|---|
| `tree` | `CmsPageNode[]` | The page tree |
| `createUrl` | `string` | The URL of the new page form |
| `breadcrumbs` | `CmsBreadcrumb[]` | |

The tree is rendered with the `TreeView` component of FormBuilder.

### cms/pages/Edit

| Prop | Type | Description |
|---|---|---|
| `page` | object | `id`, `title`, `path`, `url`, `status`, `statusLabel`, `isPublished` and `isHome` |
| `breadcrumbs` | `CmsBreadcrumb[]` | |
| `form` | `FormSchema` | The page form |
| `write` | `{ blockedReason: string \| null }` | Why the user may not change the page, or `null` |
| `blocks` | `TableData` | The block table |
| `blockTypes` | `{ key, label, createUrl }[]` | One entry per registered, enabled block type |
| `revisions` | `TableData` | The revision table |
| `publication` | object | See below |
| `pendingRevision` | object \| `null` | The pending revision of a published page, see below, or `null` |
| `previewUrl` | `string` | The signed preview link |
| `deletion` | `{ url: string, blockedReason: string \| null }` | The delete URL and why deleting is not possible |

`publication` holds:

| Key | Type | Description |
|---|---|---|
| `canPublish` | `boolean` | Whether the user has `cms.pages.publish` |
| `publishUrl` | `string` | |
| `unpublishUrl` | `string` | |
| `homeUrl` | `string` | |
| `homeBlockedReason` | `string \| null` | Why the page cannot become the home page |
| `unpublishBlockedReason` | `string \| null` | Why the page cannot be taken offline |
| `publishAt` | `string \| null` | ISO 8601 |
| `unpublishAt` | `string \| null` | ISO 8601 |
| `scheduleError` | `string \| null` | Why the last planned change failed |
| `scheduleForm` | `FormSchema \| null` | The schedule form, `null` without `cms.pages.publish` |

`pendingRevision` holds (built by `J87NL\VueCms\Support\PendingRevisionSummary`):

| Key | Type | Description |
|---|---|---|
| `author` | `string` | The name of the user who started it, or "Unknown" |
| `updatedAt` | `string \| null` | ISO 8601, the last change |
| `title` | `string` | The page title in the pending revision |
| `fields` | `string[]` | Translated labels of the page fields that differ from the live page |
| `blocks` | `{ added: number, changed: number, removed: number, reordered: boolean }` | Block changes compared with the live page |
| `publishUrl` | `string` | |
| `discardUrl` | `string` | |

### cms/media/Index

| Prop | Type |
|---|---|
| `media` | `TableData` |
| `createUrl` | `string` |
| `breadcrumbs` | `CmsBreadcrumb[]` |

### cms/menus/Index

| Prop | Type |
|---|---|
| `menus` | `{ key: string, label: string, items: TableData, createUrl: string }[]` |
| `breadcrumbs` | `CmsBreadcrumb[]` |

### cms/redirects/Index

| Prop | Type |
|---|---|
| `redirects` | `TableData` |
| `createUrl` | `string` |
| `importUrl` | `string` |
| `breadcrumbs` | `CmsBreadcrumb[]` |

`FormSchema` comes from `@form-builder/types/form-builder`, `TableData` from
`@table-builder/types/table-builder`.
