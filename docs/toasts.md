# Toasts

After a successful action the controllers flash a message with `Inertia::flash()` and redirect. The key is
[`vue-cms.toast_flash_key`](configuration.md#toasts), default `toast`. The value is:

```php
[
    'type' => 'success',
    'message' => 'Page saved.',
]
```

The message is already translated. Only success messages are flashed this way. Errors arrive as validation
errors, see [Host app contract](host-app-contract.md#validation-errors).

One exception: a CSV import that fails still flashes a success toast, "No redirects imported.", next to the `import`
error. See [Redirects](screens/redirects.md#csv-import).

Show the toast from your app, for example by listening to the Inertia `flash` event once in `app.ts`:

```ts
import { router } from '@inertiajs/vue3';

router.on('flash', (event) => {
    const toast = event.detail.flash.toast as { type: string; message: string } | undefined;

    if (toast) {
        showToast(toast.type, toast.message);
    }
});
```

`showToast` stands for your own toast component. If you change `toast_flash_key`, read that key instead.
