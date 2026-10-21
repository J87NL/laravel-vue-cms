# Media

The media screen lists the images of the site, uploads new ones, edits their alt text, caption and focal point,
and replaces their file. The images themselves are stored by `j87nl/laravel-cms`. For how it cleans, stores, converts and counts
images, see [Media](https://github.com/J87NL/laravel-cms/blob/main/docs/media.md) in the laravel-cms docs.

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
| `file` | Required file of at most `cms.media.max_upload_kilobytes` (default `10240`) |
| `alt` | Required, at most 250 characters |
| `caption` | Optional, at most 500 characters |
| `focal_x` | Required integer from 0 to 100, default 50 |
| `focal_y` | Required integer from 0 to 100, default 50 |

The file picker offers `image/jpeg`, `image/png`, `image/webp` and `image/gif`. The form does not check the type
itself. The `UploadMedia` action does, with a validation error on `file`. What it refuses is listed in
[Uploading](https://github.com/J87NL/laravel-cms/blob/main/docs/media.md#uploading).

The focal point is the part of the image that must stay visible when it is cropped, see
[Fields](https://github.com/J87NL/laravel-cms/blob/main/docs/media.md#fields).

## Editing

`GET admin/cms/media/{asset}/edit` (`cms.media.edit`) shows a preview of the image and
`J87NL\VueCms\Forms\MediaForm`, with the same `alt`, `caption`, `focal_x` and `focal_y` fields. Saving sends
`PUT` to `cms.media.update`, which runs the `UpdateMedia` action of laravel-cms.

## Replacing the file

Below the form, the edit screen has a "Bestand vervangen" (replace file) section with a file picker. Its button asks
for confirmation with the app's confirm dialog, then posts the file to `POST admin/cms/media/{asset}/replace`
(`cms.media.replace`). The request is validated with `J87NL\VueCms\Forms\MediaReplaceForm` (a required file of at
most `cms.media.max_upload_kilobytes`), and the `ReplaceMediaFile` action of laravel-cms stores the new file with the
same checks as an upload. The image keeps its id, alt text, caption and focal point, so every page that uses it shows
the new file without being changed. The old file and its conversions are deleted. See
[Replacing the file](https://github.com/J87NL/laravel-cms/blob/main/docs/media.md#replacing-the-file).

A refused file shows its error in the section and leaves the current file in place. After a replacement the screen
opens again with a toast.

There is no MCP tool to upload or replace an image; that only happens here.

## Deleting

`DELETE admin/cms/media/{asset}` (`cms.media.destroy`) runs the `DeleteMedia` action. When it refuses, the error
appears above the list.
