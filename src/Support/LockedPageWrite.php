<?php

declare(strict_types=1);

namespace J87NL\VueCms\Support;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use J87NL\Cms\Models\Page;

class LockedPageWrite
{
    /**
     * @template TResult
     *
     * @param  Closure(Page): TResult  $callback
     * @return TResult
     */
    public function run(mixed $user, Page $page, Closure $callback): mixed
    {
        return DB::transaction(function () use ($user, $page, $callback): mixed {
            $locked = Page::query()
                ->lockForUpdate()
                ->findOrFail($page->id);

            Gate::forUser($user)->authorize(PageWriteAccess::ABILITY, $locked);

            return $callback($locked);
        });
    }
}
