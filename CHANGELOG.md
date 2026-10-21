# Changelog

All notable changes to this package are documented here. The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and versions follow [Semantic Versioning](https://semver.org/).

## [Unreleased]

### Changed

- Requires `j87nl/laravel-cms` `^0.2.2`.
- Reordering and deleting menu items and editing an image run the `ReorderMenuItems`, `DeleteMenuItem` and `UpdateMedia` actions of laravel-cms. A menu reorder now also flushes the page cache.
- The "Page type" options and their validation come from `cms.schema_types` of laravel-cms. `vue-cms.schema_types` is no longer read: move a customised list to `config/cms.php`.
- The revisions panel shows the number from `cms.revisions.keep` instead of a fixed 20.
- The title pattern help uses a neutral example, and the redirect import help names every header the import skips.
- The README no longer mentions CrudBuilder, which is not a dependency.

## [0.2.0]

### Changed

- Require `j87nl/laravel-cms` `^0.2`.
- The page and redirect forms take their validation rules from `J87NL\Cms\Validation\PageRules` and `RedirectRules`, so the admin and the MCP tools of `j87nl/laravel-cms` validate the same way.
- Saving a menu item goes through `J87NL\Cms\Actions\SaveMenuItem` and reordering blocks through `J87NL\Cms\Actions\ReorderBlocks`. The error for a menu item with items below it is now `cms::validation.menu_item_has_children`.

### Removed

- The translation key `vue-cms::menus.errors.has_children`.

## [0.1.0]

First release, extracted from the j87.nl app.

### Added

- An Inertia and Vue admin for `j87nl/laravel-cms` on TranquilTools FormBuilder and TableBuilder.
- A sortable page tree, a page editor with a sortable block list, per-type block forms, revisions and publication actions.
- Planning publication and taking offline, and a preview button with a signed link.
- Media, menus, redirects with CSV import, and site settings.
- Navigation items and permissions registered in the app's config, and Dutch and English translations.

[Unreleased]: https://github.com/J87NL/laravel-vue-cms/compare/v0.2.0...HEAD
[0.2.0]: https://github.com/J87NL/laravel-vue-cms/compare/v0.1.0...v0.2.0
[0.1.0]: https://github.com/J87NL/laravel-vue-cms/releases/tag/v0.1.0
