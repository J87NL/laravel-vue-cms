<?php

declare(strict_types=1);

namespace J87NL\VueCms\Support;

use J87NL\Cms\Models\Page;

class PageOptions
{
    /**
     * @return list<array{value: int, label: string}>
     */
    public static function all(?Page $except = null): array
    {
        $pages = Page::query()
            ->orderBy('path')
            ->get([
                'id',
                'title',
                'path',
            ]);

        $options = [];

        foreach ($pages as $page) {
            if (! is_null($except) && ($page->is($except) || str_starts_with($page->path, sprintf('%s/', $except->path)))) {
                continue;
            }

            $options[] = [
                'value' => $page->id,
                'label' => sprintf('%s (/%s)', $page->title, $page->path),
            ];
        }

        return $options;
    }
}
