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

"Publish" shows for a page that is not published, "Take offline" for a published page.

"Take offline" is disabled, with the reason shown, for the home page and for the pages above it.

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
`SchedulePage` action of laravel-cms refuses a moment in the past.

The panel shows the planned moments in the user's locale. When laravel-cms could not carry out a planned
change, the panel shows the stored `schedule_error`. The change is retried every minute until the schedule or the
cause is fixed.

The planned changes themselves are carried out by laravel-cms. See the laravel-cms documentation for its
scheduler.

## Preview

The "Preview" link opens the page in a new tab, also when it is not published. The link is a temporary signed
URL made by the `PreviewController` of laravel-cms. Its lifetime is `cms.preview.minutes`, 60 minutes when the key
is missing.
