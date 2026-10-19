<?php

declare(strict_types=1);

use J87NL\Cms\Actions\SaveBlock;
use J87NL\Cms\Blocks\BlockRegistry;
use J87NL\Cms\CmsPermission;
use J87NL\Cms\Enums\PageStatus;
use J87NL\Cms\Models\Block;
use J87NL\Cms\Models\Page;
use J87NL\Cms\Models\Revision;
use J87NL\VueCms\Tables\BlockTable;
use TranquilTools\TableBuilder\Routing\SignedTableUrl;

function pageWithBlocks(PageStatus $status): Page
{
    makePage([
        'slug' => 'home',
    ]);
    $page = makePage([
        'title' => 'Target',
        'slug' => 'target',
    ], $status);

    foreach (['First', 'Second'] as $text) {
        app(SaveBlock::class)->handle($page, new Block, app(BlockRegistry::class)->find('rich_text'), [
            'html' => sprintf('<p>%s</p>', $text),
        ], null);
    }

    return $page->refresh();
}

/**
 * @return array<string, array{0: string, 1: string, 2: Closure(Page): array<int, mixed>, 3: array<string, mixed>}>
 */
function pageWrites(): array
{
    $firstBlock = fn (Page $page): Block => $page->blocks()->firstOrFail();

    return [
        'update' => ['put', 'cms.pages.update', fn (Page $page): array => [$page], ['title' => 'Changed', 'slug' => 'target']],
        'move' => ['post', 'cms.pages.move', fn (Page $page): array => [$page], ['target' => 1, 'position' => 'inside']],
        'block store' => ['post', 'cms.blocks.store', fn (Page $page): array => [$page, 'rich_text'], ['html' => '<p>New</p>']],
        'block update' => ['put', 'cms.blocks.update', fn (Page $page): array => [$page, $firstBlock($page)], ['html' => '<p>Changed</p>']],
        'block destroy' => ['delete', 'cms.blocks.destroy', fn (Page $page): array => [$page, $firstBlock($page)], []],
        'block toggle' => ['post', 'cms.blocks.toggle', fn (Page $page): array => [$page, $firstBlock($page)], []],
        'revision restore' => ['post', 'cms.revisions.restore', fn (Page $page): array => [$page, $page->revisions()->firstOrFail()], []],
    ];
}

test('an editor without the publish permission cannot change a published page', function (string $method, string $route, Closure $parameters, array $data): void {
    $page = pageWithBlocks(PageStatus::Published);
    $before = [
        $page->fresh()->title,
        Block::query()
            ->orderBy('id')->get(['data', 'is_visible'])->toArray(),
    ];

    $this->actingWithPermissions([CmsPermission::PagesEdit->value])
        ->{$method}(route($route, $parameters($page)), $data)
        ->assertForbidden();

    expect([
        $page->fresh()->title,
        Block::query()
            ->orderBy('id')->get(['data', 'is_visible'])->toArray(),
    ])->toBe($before);
})->with(pageWrites());

test('an editor without the publish permission can change a draft page', function (string $method, string $route, Closure $parameters, array $data): void {
    $page = pageWithBlocks(PageStatus::Draft);

    $this->actingWithPermissions([CmsPermission::PagesEdit->value])
        ->{$method}(route($route, $parameters($page)), $data)
        ->assertRedirect();
})->with(pageWrites());

test('an editor without the publish permission cannot reorder the blocks of a published page', function (): void {
    $page = pageWithBlocks(PageStatus::Published);

    $this->actingWithPermissions([CmsPermission::PagesEdit->value]);

    $payload = $this->get(route('cms.pages.edit', $page), $this->inertiaHeaders())
        ->assertJsonPath('props.write.blockedReason', __('vue-cms::pages.publication.write_requires_publish'))
        ->assertJsonPath('props.blocks.reorder', null)
        ->json('props.blocks');

    expect(collect($payload['rowActions'][0])->every(fn (array $action): bool => $action['disabled']))->toBeTrue();

    $publishedReorderUrl = SignedTableUrl::make('table.reorder', BlockTable::class, [
        'page' => $page->id,
    ]);

    $blocks = $page->blocks()->pluck('id');

    $this->postJson($publishedReorderUrl, [
        'id' => $blocks[1],
        'to' => $blocks[0],
    ])->assertForbidden();
});

test('an editor reorders the blocks of a draft page and a revision is recorded', function (): void {
    $page = pageWithBlocks(PageStatus::Draft);
    $blocks = $page->blocks()->pluck('id')->all();
    $revisions = Revision::query()
        ->where('page_id', $page->id)
        ->count();

    $this->actingWithPermissions([CmsPermission::PagesEdit->value]);

    $this->postJson(SignedTableUrl::make('table.reorder', BlockTable::class, [
        'page' => $page->id,
    ]), [
        'id' => $blocks[1],
        'to' => $blocks[0],
    ])->assertSuccessful();

    expect($page->blocks()->pluck('id')->all())->toBe([$blocks[1], $blocks[0]])
        ->and(Revision::query()
            ->where('page_id', $page->id)
            ->count())->toBe($revisions + 1);
});
