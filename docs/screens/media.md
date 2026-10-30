# Media

The media screen lists the images of the site, uploads new ones, edits their alt text, caption and focal point, and
replaces their file. The images themselves are stored by `j87nl/laravel-cms`. For how it cleans, stores, converts
and counts images, see [Media](https://github.com/J87NL/laravel-cms/blob/main/docs/media.md) in the laravel-cms
docs.

All media routes need the `cms.media` permission.

## Media list

`GET admin/cms/media` (`cms.media.index`) renders the `media_index` component with
`J87NL\VueCms\Tables\MediaTable`, newest first, 25 per page:

| Column | Content |
|---|---|
| Preview | A thumbnail, cropped around the focal point |
| Alternative text | `alt` |
| Caption | `caption` |
| Used | How many times the image is used, counted by the `MediaUsage` class of laravel-cms |

The search box searches `alt` and `caption`.

Each row has the actions Edit and Delete. Delete asks for confirmation. It is disabled while the image is in use,
and the `DeleteMedia` action of laravel-cms refuses it as well. What counts as a use is listed in
[Deleting](https://github.com/J87NL/laravel-cms/blob/main/docs/media.md#deleting).

## Uploading

`GET admin/cms/media/create` (`cms.media.create`) shows `J87NL\VueCms\Forms\MediaUploadForm`. It posts to
`cms.media.store`, which passes the file and the details to the `UploadMedia` action of laravel-cms.

| Field | Rules |
|---|---|
| `file` | Required file of at most `cms.media.max_upload_kilobytes` (default `10240`, no limit when it is `0`) |
| `alt` | Required, at most 250 characters |
| `caption` | Optional, at most 500 characters |
| `focal_x` | Required integer from 0 to 100, default 50 |
| `focal_y` | Required integer from 0 to 100, default 50 |

The file picker offers `image/jpeg`, `image/png`, `image/webp` and `image/gif`, and the form refuses other detected
types with the `mimetypes` rule that the form builder's `accept()` adds. The `UploadMedia` action checks the image
again, with a validation error on `file`. What it refuses is listed in
[Uploading](https://github.com/J87NL/laravel-cms/blob/main/docs/media.md#uploading).

The focal point is the part of the image that must stay visible when it is cropped, see
[Fields](https://github.com/J87NL/laravel-cms/blob/main/docs/media.md#fields).

## Editing

`GET admin/cms/media/{asset}/edit` (`cms.media.edit`) shows a preview of the image and
`J87NL\VueCms\Forms\MediaForm`, with the same `alt`, `caption`, `focal_x` and `focal_y` fields. Saving sends
`PUT` to `cms.media.update`, which runs the `UpdateMedia` action of laravel-cms.

## Replacing the file

Below the form, the edit screen has a "Bestand vervangen" (replace file) section that renders
`J87NL\VueCms\Forms\MediaReplaceForm` with the form builder, like the other media forms. Its button asks for
confirmation with the app's confirm dialog, then submits that form to `POST admin/cms/media/{asset}/replace`
(`cms.media.replace`). The request is validated with the same `MediaReplaceForm`: a required file of at
most `cms.media.max_upload_kilobytes` (no limit when it is `0`), whose detected content type is one of
`MediaReplaceForm::accept()` (`image/jpeg`, `image/png`, `image/webp` and `image/gif`; the form builder's `accept()`
adds the `mimetypes` rule). The file picker offers the same types. Then the `ReplaceMediaFile` action of laravel-cms
stores the new file with the same checks as an upload. The image keeps its id, alt text, caption and focal point, so
every page that uses it shows the new file without being changed. The old file and its conversions are deleted. See
[Replacing the file](https://github.com/J87NL/laravel-cms/blob/main/docs/media.md#replacing-the-file).

The confirmation warns that unsaved changes in the form above are lost, because the screen reloads after the
replacement. A refused file shows its error at the file field and leaves the current file in place. After a replacement
the screen opens again with a toast.

Replacing images only happens here. From laravel-cms 0.7 on, assistants can also add new images through the MCP tool [`media_upload`](https://github.com/J87NL/laravel-cms/blob/main/docs/mcp-tools.md#uploading-images), which runs the same `UploadMedia` checks as this screen. From laravel-cms 0.8 on, they can also delete images that are not in use with [`media_delete`](https://github.com/J87NL/laravel-cms/blob/main/docs/mcp-tools.md#deleting-images), through the same `DeleteMedia` as this screen.

## Deleting

`DELETE admin/cms/media/{asset}` (`cms.media.destroy`) runs the `DeleteMedia` action. When it refuses, the error
appears above the list.
