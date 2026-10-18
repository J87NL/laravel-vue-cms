<?php

declare(strict_types=1);

use J87NL\Cms\Actions\SavePage;
use J87NL\Cms\Enums\PageStatus;
use J87NL\Cms\Models\Page;
use J87NL\VueCms\Tests\TestCase;

pest()->extend(TestCase::class)->in('Feature');

/**
 * @param  array<string, mixed>  $attributes
 */
function makePage(array $attributes = [], PageStatus $status = PageStatus::Draft): Page
{
    $page = app(SavePage::class)->handle(new Page, [
        'title' => 'Page',
        ...$attributes,
    ], null);

    $page->status = $status;
    $page->save();

    return $page->refresh();
}
