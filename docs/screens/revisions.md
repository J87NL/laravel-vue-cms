# Revisions

laravel-cms keeps a revision of a page when the page or its blocks change. The page editor lists them and
can restore one. What a revision holds, and which changes record one, is described in
[Revisions](https://github.com/J87NL/laravel-cms/blob/main/docs/revisions.md) in the laravel-cms docs.

## Revision list

`J87NL\VueCms\Tables\RevisionTable` shows the revisions of the page, newest first, 10 per page:

| Column | Content |
|---|---|
| Saved at | The moment of the revision, as `d-m-Y H:i:s` |
| By | The `name` of the author, or "Unknown" |
| Title | The page title in the revision |
| Blocks | The number of blocks in the revision |

How many revisions are kept is set with `cms.revisions.keep` in laravel-cms, see
[Pruning](https://github.com/J87NL/laravel-cms/blob/main/docs/revisions.md#pruning).

## Restoring

Each row has a "Restore" action that asks for confirmation and posts to `cms.revisions.restore`. The
`RestoreRevision` action of laravel-cms replaces the page fields and blocks. The publication status stays as it
is. See [Restoring](https://github.com/J87NL/laravel-cms/blob/main/docs/revisions.md#restoring).

The action is disabled:

- on the newest revision, because that is the current version, and
- when the user may not change the page. See [Permissions](../permissions.md#changing-a-published-page).

The route uses scoped bindings: a revision of another page answers `404`. The restore runs inside a locked page
write, see [Security](../security.md#locked-page-writes).
