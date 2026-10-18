<?php

declare(strict_types=1);

namespace J87NL\VueCms\Http\Controllers;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Inertia\Inertia;
use J87NL\Cms\Models\Page;
use J87NL\VueCms\Support\LockedPageWrite;

abstract class Controller extends BaseController
{
    protected function toast(string $message): void
    {
        Inertia::flash((string) config('vue-cms.toast_flash_key', 'toast'), [
            'type' => 'success',
            'message' => $message,
        ]);
    }

    protected function userId(Request $request): ?int
    {
        $id = $request->user()?->getAuthIdentifier();

        return is_numeric($id) ? (int) $id : null;
    }

    /**
     * @template TResult
     *
     * @param  Closure(Page): TResult  $callback
     * @return TResult
     */
    protected function writeLocked(Request $request, Page $page, Closure $callback): mixed
    {
        return app(LockedPageWrite::class)->run($request->user(), $page, $callback);
    }

    protected function page(string $key): string
    {
        return (string) config(sprintf('vue-cms.pages.%s', $key));
    }
}
