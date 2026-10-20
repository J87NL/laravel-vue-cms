# Site settings

The settings screen edits the site-wide details of laravel-cms: the site name, the default texts for search engines, who
is behind the site and the extra lines for `robots.txt` and `llms.txt`. laravel-cms stores them in its `SiteSettings`
model and uses them on every page. What each field does on the site, and its fallback, is described in
[Site settings](https://github.com/J87NL/laravel-cms/blob/main/docs/site-settings.md) in the laravel-cms docs.

The routes need the `cms.settings` permission.

## Screen

`GET admin/cms/settings` (`cms.settings.edit`) shows `J87NL\VueCms\Forms\SettingsForm`. Saving sends `PUT` to
`cms.settings.update`, which runs the `SaveSiteSettings` action of laravel-cms.

The back button links to the page tree, and only shows for users with `cms.pages.edit`.

## Fields

### Site

| Field | Rules | Notes |
|---|---|---|
| `site_name` | Required, at most 120 characters | |
| `title_pattern` | At most 160 characters | The page title pattern. `:title` stands for the page title. Empty means `:title \| site name`. |
| `default_meta_description` | At most 320 characters | For pages without their own description and without a text block |
| `og_image_id` | An existing media item | The sharing image for pages without their own |
| `privacy_page_id` | An existing page | Forms on the site link to this page |
| `contact_recipient` | An email address, at most 254 characters | The address the contact form mails to |

### Who is behind it

| Field | Rules | Notes |
|---|---|---|
| `entity_type` | Required, `Person` or `Organization` | |
| `entity_name` | At most 120 characters | |
| `entity_job_title` | At most 120 characters | Only for a person |
| `entity_email` | An email address, at most 254 characters | Only used in the data for search engines |
| `entity_image_id` | An existing media item | Photo or logo |
| `entity_same_as` | At most 4000 characters | One full address per line, such as a LinkedIn or GitHub profile |

The `SaveSiteSettings` action of laravel-cms also checks that every line of `entity_same_as` is a full web
address, see [Saving](https://github.com/J87NL/laravel-cms/blob/main/docs/site-settings.md#saving).

### Search engines and AI

| Field | Rules | Notes |
|---|---|---|
| `robots_extra` | At most 4000 characters | Extra lines for `robots.txt` |
| `llms_intro` | At most 4000 characters | A short description of the site for `llms.txt`. Empty means the default description. |

All fields except `site_name` and `entity_type` are optional.
