# Spam protection

The spam protection screen sets the Cloudflare Turnstile keys of the public contact form of laravel-cms. The contact
form always uses a hidden honeypot field and a limit per IP address. Turnstile adds an extra check when both keys are
filled in. What Turnstile does on the site is described in
[Contact form](https://github.com/J87NL/laravel-cms/blob/main/docs/contact-form.md#cloudflare-turnstile) in the
laravel-cms docs.

The routes need the `cms.settings` permission, the same as the site settings.

## Screen

`GET admin/cms/settings/spam-protection` (`cms.spam-protection.edit`) shows `J87NL\VueCms\Forms\SpamProtectionForm` on
the `cms/Form` page, with a status line above the form: "Cloudflare Turnstile staat aan op het openbare
contactformulier." or "Cloudflare Turnstile staat uit. Vul een sitesleutel en een geheime sleutel in om het aan te
zetten." The status follows the `TurnstileKeys` binding of laravel-cms, so it also reflects keys an app serves from
elsewhere. Saving sends `PUT` to `cms.spam-protection.update`, which runs the `SaveSiteSettings` action of laravel-cms
with only the Turnstile keys.

The back button links to the page tree, and only shows for users with `cms.pages.edit`.

The package does not register a navigation item for this screen. Link it from your settings menu, next to the site
settings, for users with `cms.settings`, see [Navigation](../navigation.md#screens-without-an-item).

## Fields

| Field | Rules | Notes |
|---|---|---|
| `contact_turnstile_site_key` | At most 255 characters | From the Turnstile widget in the Cloudflare dashboard. Empty switches Turnstile off. Stored trimmed. |
| `contact_turnstile_secret_key` | At most 255 characters | Stored encrypted and never shown again |

The secret key is never sent back to the browser. After saving, the field only shows that a secret is stored. Leaving
it empty keeps the stored secret, and the remove checkbox (`remove_contact_turnstile_secret_key`) clears it.

Saving flushes the page cache of laravel-cms, so the widget appears on or disappears from the contact form at once.
