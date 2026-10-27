# Translations

The package ships English (`en`) and Dutch (`nl`) translations in its `lang` folder, under the namespace
`vue-cms`.

| File | Used for |
|---|---|
| `blocks.php` | The block list and block forms |
| `common.php` | Shared words such as "Back" |
| `media.php` | The media screen |
| `menus.php` | The menus screen |
| `navigation.php` | The [navigation](navigation.md) labels |
| `pages.php` | The page tree, the page editor and publication |
| `redirects.php` | The redirects screen and the CSV import |
| `revisions.php` | The revision list |
| `settings.php` | The site settings |
| `spam_protection.php` | The spam protection screen |

Some messages come from laravel-cms and use its `cms::` namespace, such as the validation errors of its actions and the
labels of its enums. Its files are listed in
[Translations](https://github.com/J87NL/laravel-cms/blob/main/docs/rendering.md#translations) in the laravel-cms docs.

## On the server

The service provider loads the folder with `loadTranslationsFrom()`, so the forms, tables and flash messages use
`__('vue-cms::pages.flash.saved')` and the like in the current app locale.

## In Vue

The Vue pages call `$t('vue-cms::...')` and `trans('vue-cms::...')` from `laravel-vue-i18n`. The i18n plugin must
see the package's `lang` folder. One way is a symlink:

```bash
mkdir -p lang/vendor
ln -s ../../vendor/j87nl/laravel-vue-cms/lang lang/vendor/vue-cms
```
