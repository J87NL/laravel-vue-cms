# Changelog

All notable changes to this package are documented here. The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and versions follow [Semantic Versioning](https://semver.org/).

## [Unreleased]

## [0.4.0] - 2026-10-23

### Added

- The "Changes waiting to be published" panel of the page editor shows what the pending revision changes compared with the live page: changed page fields with their live and new value (a word-level diff for text fields), and every block with its status (added, removed, changed, unchanged), a position change, a visibility change and its changed values by form label, rich text as a word-level diff of the plain text. The comparison comes from the `DiffPendingRevision` action of laravel-cms; the new component `resources/js/components/RevisionDiff.vue` renders it.
- The comparison flags a rich text change that leaves the text alone ("only the formatting or a link changes") and lists changed link targets, old and new, one per line.
- A "Preview with changes" link on that panel, shown with `cms.pages.edit`, opens a signed, expiring, `noindex` preview of the page with the pending revision applied (`PreviewController::pendingUrl()` of laravel-cms). The preview needs a signed-in user with `cms.pages.edit` and stops working once the revision is no longer pending.
- `pendingRevision.diff` and `pendingRevision.previewUrl` props on `cms/pages/Edit`, with the types `CmsRevisionDiff`, `CmsBlockDiff`, `CmsValueDiff` and `CmsWordSegment` in `@cms/types`.
- Translations under `vue-cms::revisions.diff`.

### Changed

- The pending revision summary (`fields` and the block counts) now compares with the live page, as publishing would apply it, instead of with the page as it was when the proposal started.
- Requires `j87nl/laravel-cms` `^0.4` (for `DiffPendingRevision` and `PreviewController::pendingUrl()`). Update both packages together: `composer update j87nl/laravel-vue-cms j87nl/laravel-cms`.

## [0.3.0] - 2026-10-22

### Added

- The media edit screen has a "Bestand vervangen" (replace file) section. After a confirmation it posts to the new route `cms.media.replace`, which gives the image a new file through the `ReplaceMediaFile` action of laravel-cms. The image keeps its id, alt text, caption and focal point, so every block that uses it shows the new file. The route needs `cms.media`, like the other media routes.
- `MediaReplaceForm` validates the replacement file: required, at most `cms.media.max_upload_kilobytes`, and one of the types in `MediaReplaceForm::accept()` (the form builder's `accept()` adds the `mimetypes` rule).
- The `cms/Form` page takes an optional `replace` prop for that section: the `MediaReplaceForm` schema, rendered with the form builder.
- The page editor shows the pending revision of a published page (changed fields, added, changed and removed blocks, new block order) with "Publish changes" and "Discard changes" buttons. Both confirm first and need `cms.pages.publish` and the `cms.pages.write` gate. New routes `cms.pages.pending.publish` and `cms.pages.pending.discard`, and the `pendingRevision` prop of `cms/pages/Edit`.

### Changed

- `MediaUploadForm` no longer adds a `max` rule when `cms.media.max_upload_kilobytes` is `0`, so `0` means no limit as documented instead of refusing every file.
- Requires `j87nl/laravel-cms` `^0.3`, which must be tagged first. Update both packages together: `composer update j87nl/laravel-vue-cms j87nl/laravel-cms`.
- Requires `j87nl/laravel-cms` `^0.3` for pending revisions.
- The revision list leaves out the pending revision.
- "Publish", "Take offline" and the schedule form also check the `cms.pages.write` gate, because taking a page offline now applies its pending revision.

## [0.2.1] - 2026-10-21

### Removed

- The `vue-cms.schema_types` config key. The "Page type" options and their validation now come from `cms.schema_types` of laravel-cms. **Upgrade:** if your published `config/vue-cms.php` customises `schema_types`, move that list to `schema_types` in `config/cms.php` (publish it with `php artisan vendor:publish --tag=cms-config` if needed), then remove it from `config/vue-cms.php`.

### Changed

- Requires `j87nl/laravel-cms` `^0.2.2`, which must be tagged first. Update both packages together: `composer update j87nl/laravel-vue-cms j87nl/laravel-cms` (or `composer update j87nl/laravel-vue-cms -W`).
- Reordering and deleting menu items and editing an image run the `ReorderMenuItems`, `DeleteMenuItem` and `UpdateMedia` actions of laravel-cms. A menu reorder now also flushes the page cache.
- The revisions panel shows the number from `RecordRevision::keep()` (`cms.revisions.keep`, at least 1) instead of a fixed 20, with a singular form for 1. The page editor gets it as the `revisionsDescription` prop.
- The image form uses the `MediaRules` of laravel-cms.
- A menu reorder with an item of another menu or a malformed id answers `422` on `id` and changes nothing.
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

[Unreleased]: https://github.com/J87NL/laravel-vue-cms/compare/v0.4.0...HEAD
[0.4.0]: https://github.com/J87NL/laravel-vue-cms/compare/v0.3.0...v0.4.0
[0.3.0]: https://github.com/J87NL/laravel-vue-cms/compare/v0.2.1...v0.3.0
[0.2.1]: https://github.com/J87NL/laravel-vue-cms/compare/v0.2.0...v0.2.1
[0.2.0]: https://github.com/J87NL/laravel-vue-cms/compare/v0.1.0...v0.2.0
[0.1.0]: https://github.com/J87NL/laravel-vue-cms/releases/tag/v0.1.0
