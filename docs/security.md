# Security

This page covers the admin. The security of the public website, the stored content and the MCP tools belongs to
laravel-cms, see its [Security](https://github.com/J87NL/laravel-cms/blob/main/docs/security.md) page. That includes the
rich text sanitizer, the security headers and the link and upload checks the admin relies on.

## Authentication and permissions

Every admin route runs behind `vue-cms.admin.middleware` (default `web` and `auth`) and a `can:` check for one of
the CMS permissions. Keep `auth`, or an equivalent, in that list. See [Routes](routes.md) and
[Permissions](permissions.md).

The Vue pages hide buttons with `$can()`, but that is only for display. The server checks every request again.

## Published pages

Without `cms.pages.publish` a user cannot change, move, reorder or restore a published page. The check is the
`cms.pages.write` gate ability, see [Permissions](permissions.md#changing-a-published-page). Deleting a published
page is a separate check, see [Deleting a page](permissions.md#deleting-a-page).

## Locked page writes

Changes to a page and its blocks run through `J87NL\VueCms\Support\LockedPageWrite`. It opens a database
transaction, loads the page again with `lockForUpdate()` and checks `cms.pages.write` on that fresh copy before it
runs the change:

```php
use J87NL\Cms\Models\Page;
use J87NL\VueCms\Support\LockedPageWrite;

app(LockedPageWrite::class)->run($user, $page, function (Page $locked): void {
    // change $locked
});
```

So a page that was published between loading the editor and saving is checked in its new state. The page form,
moving pages, every block action, block reordering and restoring a revision use it.

## Scoped bindings

Routes that take a page and one of its blocks or revisions, or a menu and one of its items, use scoped bindings.
A block, revision or item that belongs to another parent answers `404`, so permission on one page does not reach
another page's content through the URL.

## TableBuilder routes

Reordering blocks and menu items posts to the reorder route of TableBuilder. That route checks a signed URL made
when the table was rendered, then the table's own authorization:

| Table | `authorize()` | Reorder |
|---|---|---|
| `BlockTable` | `cms.pages.edit` | `cms.pages.write` for the page |
| `MenuItemTable` | `cms.menus` | `cms.menus` |
| `MediaTable` | `cms.media` | not reorderable |
| `RedirectTable` | `cms.redirects` | not reorderable |
| `RevisionTable` | `cms.pages.edit` | not reorderable |

These checks need a logged-in user inside the TableBuilder routes. Set `table-builder.routes.middleware` to
`['web', 'auth']`, see [Installation](installation.md).

## Links and uploads

- Menu item links must pass the `SafeLink` rule of laravel-cms, see
  [Links](https://github.com/J87NL/laravel-cms/blob/main/docs/security.md#links).
- Uploads are limited to `cms.media.max_upload_kilobytes`. laravel-cms checks and cleans the image itself: it refuses
  other types than JPEG, PNG, WebP and GIF, animated GIFs and images over its pixel limit. See
  [Uploading](https://github.com/J87NL/laravel-cms/blob/main/docs/media.md#uploading).
- The CSV import only accepts `csv` and `txt` files up to 1024 KB.
- The media thumbnail in the list escapes its URL, alt text and object position.

## Preview links

The preview link on the page editor is a temporary signed URL from laravel-cms. Anyone who has the link can see the page
until it expires, also when the page is not published. The same holds for the "Preview with changes" link of a
pending revision. Both links are only handed out on the page editor, which needs `cms.pages.edit`. See
[Previews](https://github.com/J87NL/laravel-cms/blob/main/docs/previews.md).
