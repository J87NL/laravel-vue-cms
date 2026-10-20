# Blocks

A page is built from blocks, shown from top to bottom. The block types, their fields, their validation and their
rendering all come from the block registry of `j87nl/laravel-cms`. This package shows them in the page editor.
See the laravel-cms documentation for how to register a block type.

All block routes need the `cms.pages.edit` permission and write access to the page. See
[Permissions](../permissions.md#changing-a-published-page).

## Block list

The page editor shows the blocks of the page in `J87NL\VueCms\Tables\BlockTable`:

| Column | Content |
|---|---|
| Type | The translated label of the block type, or the raw type key when the type is not registered or is disabled |
| Content | The first 80 characters of the block's plain text. For a block without text whose type implements `ReferencesMedia`, the number of images. |
| Visible | Whether the block is shown on the site |

Each row has the actions Edit, Hide or Show, and Delete. Delete asks for confirmation. The edit link is left out
for a block whose type is not registered or is disabled; such a block shows its raw type key.

### Reordering

Blocks can be dragged into a new order. The table posts to the TableBuilder reorder route, which calls
`BlockTable::performReorder()`. That runs the `ReorderBlocks` action of laravel-cms inside a locked page write,
which also records a revision.

Reordering is turned off, and every row action is disabled with the reason, when the user may not change the
page.

## Adding a block

Below the list the editor shows a button per registered, enabled block type. A button opens
`GET admin/cms/pages/{page}/blocks/create/{type}` (`cms.blocks.create`).

The form is `J87NL\VueCms\Forms\BlockForm`: the `fields()` of the block type plus a save button. Saving posts to
`cms.blocks.store`. The request is validated with the `rules()` of the block type and stored with the `SaveBlock`
action of laravel-cms.

An unknown type key, or a type listed in `cms.blocks.disabled`, answers `404` on the create, store, edit and update
routes.

## Editing a block

`GET admin/cms/pages/{page}/blocks/{block}/edit` (`cms.blocks.edit`) fills the same form with the block data.
Saving sends `PUT` to `cms.blocks.update`.

## Hiding and deleting

| Action | Route | laravel-cms action |
|---|---|---|
| Hide or show | `POST` `cms.blocks.toggle` | `ToggleBlockVisibility` |
| Delete | `DELETE` `cms.blocks.destroy` | `DeleteBlock` |

The block routes use scoped bindings: a block of another page answers `404`.

Every write runs inside a locked page write. See [Security](../security.md#locked-page-writes).
