# Menus

The menus screen edits the items of every menu the site has. Which menus exist is set in laravel-cms with the
`cms.menus` config key (the menu keys, such as `main` and `footer`).

All menu routes need the `cms.menus` permission.

## Menus overview

`GET admin/cms/menus` (`cms.menus.index`) renders the `menus_index` component. It shows one panel per key in
`cms.menus`. A menu that does not exist yet is created through `Menu::forKey()` of laravel-cms.

Each panel holds a `J87NL\VueCms\Tables\MenuItemTable`:

| Column | Content |
|---|---|
| Label | The label of the item. Items below another item start with a dash. |
| Link | The address the item points at |
| Status | "Visible", or "Hidden" when the linked page, or a page above it, is not published |

The rows can be dragged into a new order, which updates `sort_order`. Each row has the actions Edit and Delete.

## Menu item form

`GET admin/cms/menus/{menu}/items/create` (`cms.menu-items.create`) shows
`J87NL\VueCms\Forms\MenuItemForm`. The `{menu}` parameter is the menu key.

| Field | Rules |
|---|---|
| `link_type` | Required, `page` or `url` (the constants `MenuItemForm::LINK_PAGE` and `MenuItemForm::LINK_URL`). Default `page`. |
| `page_id` | Shown for `page`. Required for `page`, an existing page id. |
| `url` | Shown for `url`. Required for `url`, at most 2048 characters, and must pass the `SafeLink` rule of laravel-cms. |
| `label` | Required for `url`, at most 80 characters. For a page, leave it empty to use the page's menu title. |
| `parent_id` | Optional. Only a top-level item of the same menu, other than the item itself. |

The form help describes a safe link as a path such as `/contact`, or an address starting with `https://`,
`mailto:` or `tel:`.

Saving posts to `cms.menu-items.store`, or sends `PUT` to `cms.menu-items.update`. Both go through the
`SaveMenuItem` action of laravel-cms. A new item goes at the end of its menu. An item that has items below it
cannot be placed under another item; the error is `cms::validation.menu_item_has_children`.

## Deleting

Delete asks for confirmation and sends `DELETE` to `cms.menu-items.destroy`. The items below the deleted item are
deleted with it.

The item routes use scoped bindings: an item of another menu answers `404`.
