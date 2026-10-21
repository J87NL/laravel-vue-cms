# Publication

The publication panel at the top of the page editor publishes a page, takes it offline, makes it the home page
and plans those changes ahead. It also holds the preview link.

Every publication route needs both `cms.pages.edit` and `cms.pages.publish`. Users without `cms.pages.publish`
see the buttons disabled, with a notice that explains why. See [Permissions](../permissions.md).

## Publish and take offline

| Button | Route | What happens |
|---|---|---|
| Publish | `POST` `cms.pages.publish` | `ChangePageStatus` sets the status to `published` |
| Take offline | `POST` `cms.pages.unpublish` | `ChangePageStatus` sets the status to `unpublished` |

The statuses, and when a page is public, are described in
[Statuses](https://github.com/J87NL/laravel-cms/blob/main/docs/pages.md#statuses) in the laravel-cms docs.

"Publish" shows for a page that is not published, "Take offline" for a published page.

"Take offline" is disabled, with the reason shown, for the home page and for the pages above it. The rules of the
home page are listed in [The home page](https://github.com/J87NL/laravel-cms/blob/main/docs/pages.md#the-home-page).

## Pending changes

A published page can hold a pending revision: changes that are not live yet, proposed through the MCP draft tools of
laravel-cms (see [Draft and live mode](https://github.com/J87NL/laravel-cms/blob/main/docs/mcp-tools.md#draft-and-live-mode)).
The page editor then shows a "Changes waiting to be published" panel under the publication panel, with who proposed
them, who changed them last and when, which page fields change, how many blocks are added, changed and removed, and
whether their order changes. These are counted against the page as it was when the proposal started, so edits made in
the editor in the meantime are not listed. The fields and blocks below the panel still show the live page.

| Button | Route | What happens |
|---|---|---|
| Publish changes | `POST` `cms.pages.pending.publish` | `PublishPage` replaces the live page fields and blocks with the pending ones |
| Discard changes | `DELETE` `cms.pages.pending.discard` | `DiscardPendingRevision` throws the pending revision away |

Both ask for confirmation first and need `cms.pages.edit` and `cms.pages.publish`, and the `cms.pages.write` gate
(see [Permissions](../permissions.md#changing-a-published-page)); without the publish permission the buttons are
disabled with a notice. Publishing applies only what the proposal changed: edits made directly on the live page in the
meantime stay, unless the proposal changed the same field or block. Taking the page offline applies the proposal to
the offline page, so the panel only shows on a published page.

## Home page

"Make home page" posts to `cms.pages.home` and runs the `MakeHomePage` action of laravel-cms. The button is
disabled, with the reason shown, when the page is not visible to the public. On the home page itself the panel
says "This is the home page." instead.

## Planning

Users with `cms.pages.publish` see a schedule form, `J87NL\VueCms\Forms\SchedulePageForm`. It posts to
`cms.pages.schedule`.

| Field | Shown | Notes |
|---|---|---|
| `publish_at` | Only while the page is not published | The page goes online by itself at this moment |
| `unpublish_at` | Always | The page goes offline by itself at this moment |

Both fields are optional dates, entered in the browser's timezone. An empty field clears that moment. The
`SchedulePage` action of laravel-cms checks the moments, for example it refuses a moment in the past. See
[Planning a change](https://github.com/J87NL/laravel-cms/blob/main/docs/scheduling.md#planning-a-change).

The panel shows the planned moments in the user's locale. When laravel-cms could not carry out a planned
change, the panel shows the stored `schedule_error`.

The planned changes themselves are carried out by the `cms:publish-scheduled` command of laravel-cms, which needs
the Laravel scheduler. See [Scheduling](https://github.com/J87NL/laravel-cms/blob/main/docs/scheduling.md).

## Preview

The "Preview" link opens the page in a new tab, also when it is not published. The link is a temporary signed
URL made by the `PreviewController` of laravel-cms. Its lifetime and what a preview renders are described in
[Previews](https://github.com/J87NL/laravel-cms/blob/main/docs/previews.md).
