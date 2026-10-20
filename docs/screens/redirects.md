# Redirects

The redirects screen manages old addresses that forward to a new one. laravel-cms adds redirects by itself when a public
page is renamed or moved; this screen adds, edits, deletes and imports them by hand. How redirects apply, how they are
normalized and how chains are resolved is described in
[Redirects](https://github.com/J87NL/laravel-cms/blob/main/docs/redirects.md) in the laravel-cms docs.

All redirect routes need the `cms.redirects` permission.

## Redirect list

`GET admin/cms/redirects` (`cms.redirects.index`) renders the `redirects_index` component with
`J87NL\VueCms\Tables\RedirectTable`, sorted by `from_path`, 50 per page:

| Column | Content |
|---|---|
| Old address | The `from_path` as a URL |
| New address | `to` |
| Type | `status_code` |
| Source | Where the redirect came from: automatic, manual or import |
| Hits | How often the redirect was used |
| Last hit | The last use, as `D MMM YYYY HH:mm` |

The search box searches `from_path` and `to`. Each row has the actions Edit and Delete. Delete asks for
confirmation.

## Redirect form

`GET admin/cms/redirects/create` (`cms.redirects.create`) shows `J87NL\VueCms\Forms\RedirectForm`. The rules come
from `J87NL\Cms\Validation\RedirectRules`, the same rules the laravel-cms MCP tools use.

| Field | Notes |
|---|---|
| `from_path` | Required. A path such as `/old-page`, or a full address on this site. |
| `to` | Required. A path starting with `/`, or a full address starting with `http://` or `https://`. |
| `status_code` | Required, `301` or `302`. A new redirect starts at `301`. |

Saving posts to `cms.redirects.store`, or sends `PUT` to `cms.redirects.update`. Both go through the
`SaveRedirect` action of laravel-cms with the source `manual`. When it refuses a redirect, for example one that
would loop, see [Manual redirects](https://github.com/J87NL/laravel-cms/blob/main/docs/redirects.md#manual-redirects).

## CSV import

`GET admin/cms/redirects/import` (`cms.redirects.import`) shows `J87NL\VueCms\Forms\RedirectImportForm` with one
field:

| Field | Rules |
|---|---|
| `file` | Required, a `csv` or `txt` file of at most 1024 KB |

It posts to `cms.redirects.import.store`, which runs the `ImportRedirects` action of laravel-cms. Each line holds
the old address, the new address and an optional status code, separated by a comma or a semicolon:

```csv
from,to,status
/old-page,/new-page,301
/summer,https://example.com/summer,302
```

The full file format is described in
[CSV import](https://github.com/J87NL/laravel-cms/blob/main/docs/redirects.md#csv-import).

If any line contains an error, nothing is imported. The list then shows an error under the `import` key with the
number of failed lines and the first 10 of them, as `line: message`.
