# Pages

The pages screen shows all CMS pages as a tree. The page editor holds the page fields, the blocks, the
publication actions and the revisions of one page. The rules behind them, such as paths, statuses and the home
page, belong to laravel-cms, see [Pages](https://github.com/J87NL/laravel-cms/blob/main/docs/pages.md) in its docs.

All page routes need the `cms.pages.edit` permission. See [Permissions](../permissions.md).

## Page tree

`GET admin/cms/pages` (`cms.pages.index`) renders the `pages_index` component with the full tree. Pages are
sorted by `sort_order`, then `id`. Each node shows the path, the status, a badge for the home page and an edit
link. Clicking a node opens the editor.

### Moving pages

Users with `cms.pages.edit` can drag a node above, below or onto another node. The page posts to
`cms.pages.move`:

| Field | Rule |
|---|---|
| `target` | Required, the id of an existing page |
| `position` | Required, one of `before`, `after` or `inside` |

The move runs through the `MovePage` action of laravel-cms, see
[Moving pages](https://github.com/J87NL/laravel-cms/blob/main/docs/pages.md#moving-pages).

A published page can only be moved by a user who also has `cms.pages.publish`. The tree knows this up front: a
node carries a `moveBlockedReason`, and the page shows that reason instead of posting. Users without
`cms.pages.publish` see a notice above the tree.

## New page

`GET admin/cms/pages/create` (`cms.pages.create`) shows the page form. Saving posts to `cms.pages.store`. The
`SavePage` action of laravel-cms stores the page as a draft, and the browser goes to the editor of the new page.
See [Creating and updating](https://github.com/J87NL/laravel-cms/blob/main/docs/pages.md#creating-and-updating).

## Page form

`J87NL\VueCms\Forms\PageForm` holds two sections. The validation rules of every field except `schema_type` come
from `J87NL\Cms\Validation\PageRules`, the same rules the laravel-cms MCP tools use. What each field does is
described in [Fields](https://github.com/J87NL/laravel-cms/blob/main/docs/pages.md#fields).

| Section | Field | Notes |
|---|---|---|
| Page | `title` | Required |
| Page | `slug` | Left empty, it is made from the title |
| Page | `parent_id` | A searchable list of pages. The page itself and the pages below it are left out. |
| Page | `menu_title` | A shorter title for menus |
| Search engines and sharing | `meta_title` | |
| Search engines and sharing | `og_image_id` | A searchable list of media |
| Search engines and sharing | `meta_description` | |
| Search engines and sharing | `canonical_url` | |
| Search engines and sharing | `schema_type` | One of [`cms.schema_types`](../configuration.md#settings-read-from-laravel-cms), or empty |
| Search engines and sharing | `noindex` | A toggle |

## Page editor

`GET admin/cms/pages/{page}/edit` (`cms.pages.edit`) renders the `pages_edit` component. From top to bottom it
shows:

- A header with the status, the home badge, the path, a "View page" link (published pages only) and a
  "Preview" link.
- The [publication](publication.md) panel.
- The page form. Saving sends `PUT` to `cms.pages.update`.
- The [blocks](blocks.md) of the page.
- The [revisions](revisions.md) of the page.
- A "Delete page" panel.

When the user may not change the page, the editor shows the reason, disables every form field and leaves out the
save button. This happens for a published page when the user lacks `cms.pages.publish`. See
[Permissions](../permissions.md#changing-a-published-page).

## Deleting a page

The "Delete page" button asks for confirmation and sends `DELETE` to `cms.pages.destroy`. The `DeletePage` action of
laravel-cms removes the page and what belongs to it, see
[Deleting pages](https://github.com/J87NL/laravel-cms/blob/main/docs/pages.md#deleting-pages).

The button is disabled, with the reason shown, when:

- the page is the home page,
- the page has pages below it, or
- the page is published and the user lacks `cms.pages.publish`.

The server refuses a published page with `403` for users without `cms.pages.publish`.
