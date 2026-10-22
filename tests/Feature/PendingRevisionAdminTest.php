<?php

declare(strict_types=1);

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Gate;
use J87NL\Cms\Actions\SaveBlock;
use J87NL\Cms\Blocks\BlockRegistry;
use J87NL\Cms\CmsPermission;
use J87NL\Cms\Enums\PageStatus;
use J87NL\Cms\Models\Block;
use J87NL\Cms\Models\Page;
use J87NL\Cms\Models\Revision;
use J87NL\Cms\Pages\PendingRevisionEditor;
use J87NL\VueCms\Support\PageWriteAccess;

function pendingAdminPage(): array
{
    $page = makePage([
        'title' => 'Live',
        'slug' => 'live',
    ], PageStatus::Published);

    $registry = app(BlockRegistry::class);
    $kept = app(SaveBlock::class)->handle($page, new Block, $registry->find('rich_text'), ['html' => '<p>Blijft</p>'], null);
    $changed = app(SaveBlock::class)->handle($page, new Block, $registry->find('rich_text'), ['html' => '<p>Oud</p>'], null);
    $removed = app(SaveBlock::class)->handle($page, new Block, $registry->find('rich_text'), ['html' => '<p>Weg</p>'], null);

    $editor = app(PendingRevisionEditor::class);
    $editor->updatePage($page, ['title' => 'Nieuw', 'meta_description' => 'Beschrijving'], null);
    $editor->updateBlock($page, $changed->id, $registry->find('rich_text'), ['html' => '<p>Nieuw</p>'], null);
    $editor->removeBlock($page, $removed->id, null);
    $editor->addBlock($page, $registry->find('rich_text'), ['html' => '<p>Erbij</p>'], null);

    return [
        $page,
        $kept,
        $changed,
        $removed,
    ];
}

test('the page editor shows a summary of the pending revision', function (): void {
    [$page] = pendingAdminPage();

    $this->actingWithPermissions([
        CmsPermission::PagesEdit->value,
        CmsPermission::PagesPublish->value,
    ]);

    $this->get(route('cms.pages.edit', $page), $this->inertiaHeaders())
        ->assertOk()
        ->assertJsonPath('props.pendingRevision.title', 'Nieuw')
        ->assertJsonPath('props.pendingRevision.fields', [
            __('vue-cms::pages.fields.title'),
            __('vue-cms::pages.fields.meta_description'),
        ])
        ->assertJsonPath('props.pendingRevision.blocks.added', 1)
        ->assertJsonPath('props.pendingRevision.blocks.changed', 1)
        ->assertJsonPath('props.pendingRevision.blocks.removed', 1)
        ->assertJsonPath('props.pendingRevision.blocks.reordered', false)
        ->assertJsonPath('props.pendingRevision.publishUrl', route('cms.pages.pending.publish', $page))
        ->assertJsonPath('props.pendingRevision.discardUrl', route('cms.pages.pending.discard', $page));
});

test('the page editor has no pending revision when there is none', function (): void {
    $page = makePage([
        'title' => 'Live',
    ], PageStatus::Published);

    $this->actingWithPermissions([
        CmsPermission::PagesEdit->value,
    ]);

    $this->get(route('cms.pages.edit', $page), $this->inertiaHeaders())
        ->assertOk()
        ->assertJsonPath('props.pendingRevision', null);
});

test('a publisher publishes the pending revision', function (): void {
    [$page, $kept, $changed, $removed] = pendingAdminPage();

    $this->actingWithPermissions([
        CmsPermission::PagesEdit->value,
        CmsPermission::PagesPublish->value,
    ]);

    $this->post(route('cms.pages.pending.publish', $page))
        ->assertRedirect(route('cms.pages.edit', $page));

    $html = Block::query()
        ->where('page_id', $page->id)
        ->orderBy('sort_order')
        ->get()
        ->map(fn (Block $block): string => $block->data['html'])
        ->all();

    expect($page->fresh()->title)->toBe('Nieuw')
        ->and($html)->toBe([
            '<p>Blijft</p>',
            '<p>Nieuw</p>',
            '<p>Erbij</p>',
        ])
        ->and($kept->fresh())->not->toBeNull()
        ->and($changed->fresh()->data['html'])->toBe('<p>Nieuw</p>')
        ->and($removed->fresh())->toBeNull()
        ->and($page->pendingRevision()->exists())->toBeFalse();
});

test('a publisher discards the pending revision', function (): void {
    [$page, , $changed] = pendingAdminPage();

    $this->actingWithPermissions([
        CmsPermission::PagesEdit->value,
        CmsPermission::PagesPublish->value,
    ]);

    $this->delete(route('cms.pages.pending.discard', $page))
        ->assertRedirect(route('cms.pages.edit', $page));

    expect($page->pendingRevision()->exists())->toBeFalse()
        ->and($page->fresh()->title)->toBe('Live')
        ->and($changed->fresh()->data['html'])->toBe('<p>Oud</p>');
});

test('an editor without the publish permission sees the pending revision but cannot publish or discard it', function (): void {
    [$page] = pendingAdminPage();

    $this->actingWithPermissions([
        CmsPermission::PagesEdit->value,
    ]);

    $this->get(route('cms.pages.edit', $page), $this->inertiaHeaders())
        ->assertOk()
        ->assertJsonPath('props.pendingRevision.title', 'Nieuw')
        ->assertJsonPath('props.publication.canPublish', false);

    $this->post(route('cms.pages.pending.publish', $page))->assertForbidden();
    $this->delete(route('cms.pages.pending.discard', $page))->assertForbidden();

    expect($page->fresh()->title)->toBe('Live')
        ->and($page->pendingRevision()->exists())->toBeTrue();
});

test('publishing a published page without a pending revision shows an error', function (): void {
    $page = makePage([
        'title' => 'Live',
    ], PageStatus::Published);

    $this->actingWithPermissions([
        CmsPermission::PagesEdit->value,
        CmsPermission::PagesPublish->value,
    ]);

    $this->post(route('cms.pages.pending.publish', $page))
        ->assertSessionHasErrors('page');
});

test('the revision history leaves out the pending revision and its restore route does not find it', function (): void {
    [$page] = pendingAdminPage();
    $pending = Revision::query()
        ->pending()
        ->where('page_id', $page->id)
        ->sole();

    $this->actingWithPermissions([
        CmsPermission::PagesEdit->value,
        CmsPermission::PagesPublish->value,
    ]);

    $ids = collect($this->get(route('cms.pages.edit', $page), $this->inertiaHeaders())
        ->assertOk()
        ->json('props.revisions.rows'))
        ->pluck('id')
        ->all();

    expect($ids)->not->toContain($pending->id);

    $this->post(route('cms.revisions.restore', [$page, $pending]))
        ->assertNotFound();

    expect(Page::query()
        ->find($page->id)
        ->title)->toBe('Live');
});

test('publishing or discarding pending changes respects the page write gate', function (string $method, string $route): void {
    [$page] = pendingAdminPage();

    $this->actingWithPermissions([
        CmsPermission::PagesEdit->value,
        CmsPermission::PagesPublish->value,
    ]);

    Gate::define(PageWriteAccess::ABILITY, fn (): bool => false);

    $this->{$method}(route($route, $page))->assertForbidden();

    expect($page->fresh()->title)->toBe('Live')
        ->and($page->pendingRevision()->exists())->toBeTrue();
})->with([
    'publish' => ['post', 'cms.pages.pending.publish'],
    'discard' => ['delete', 'cms.pages.pending.discard'],
]);

test('the summary compares the pending revision with the page it started from', function (): void {
    [$page] = pendingAdminPage();

    app(SaveBlock::class)->handle($page, new Block, app(BlockRegistry::class)->find('rich_text'), ['html' => '<p>Live erbij</p>'], null);

    $this->actingWithPermissions([
        CmsPermission::PagesEdit->value,
        CmsPermission::PagesPublish->value,
    ]);

    $this->get(route('cms.pages.edit', $page), $this->inertiaHeaders())
        ->assertOk()
        ->assertJsonPath('props.pendingRevision.blocks.added', 1)
        ->assertJsonPath('props.pendingRevision.blocks.removed', 1)
        ->assertJsonPath('props.pendingRevision.editor', __('vue-cms::revisions.unknown_author'));
});

test('the summary names the user who started the changes and the user of the last change', function (): void {
    config()->set('cms.user_model', User::class);

    $alice = User::query()
        ->forceCreate([
            'name' => 'Alice',
        ]);
    $bob = User::query()
        ->forceCreate([
            'name' => 'Bob',
        ]);

    $page = makePage([
        'title' => 'Live',
    ], PageStatus::Published);

    $editor = app(PendingRevisionEditor::class);
    $editor->updatePage($page, ['title' => 'Een'], $alice->id);
    $editor->updatePage($page, ['title' => 'Twee'], $bob->id);

    $this->actingWithPermissions([
        CmsPermission::PagesEdit->value,
    ]);

    $this->get(route('cms.pages.edit', $page), $this->inertiaHeaders())
        ->assertOk()
        ->assertJsonPath('props.pendingRevision.author', 'Alice')
        ->assertJsonPath('props.pendingRevision.editor', 'Bob');

    $editor->updatePage($page, ['title' => 'Drie'], null);

    $this->get(route('cms.pages.edit', $page), $this->inertiaHeaders())
        ->assertJsonPath('props.pendingRevision.editor', __('vue-cms::revisions.unknown_author'));
});

test('publishing and taking a page offline respect the page write gate', function (string $route, PageStatus $status): void {
    $page = makePage([
        'title' => 'Pagina',
        'slug' => 'pagina',
    ], $status);

    $this->actingWithPermissions([
        CmsPermission::PagesEdit->value,
        CmsPermission::PagesPublish->value,
    ]);

    Gate::define(PageWriteAccess::ABILITY, fn (): bool => false);

    $this->post(route($route, $page))->assertForbidden();

    expect($page->fresh()->status)->toBe($status);
})->with([
    'publish' => ['cms.pages.publish', PageStatus::Draft],
    'unpublish' => ['cms.pages.unpublish', PageStatus::Published],
]);

test('scheduling respects the page write gate', function (): void {
    $page = makePage([
        'title' => 'Pagina',
        'slug' => 'pagina',
    ], PageStatus::Published);

    $this->actingWithPermissions([
        CmsPermission::PagesEdit->value,
        CmsPermission::PagesPublish->value,
    ]);

    Gate::define(PageWriteAccess::ABILITY, fn (): bool => false);

    $this->post(route('cms.pages.schedule', $page), [
        'unpublish_at' => now()->addDay()->format('Y-m-d H:i:s'),
    ])->assertForbidden();

    expect($page->fresh()->unpublish_at)->toBeNull();
});

test('the page editor shows what the pending revision changes compared with the live page', function (): void {
    [$page, $kept, $changed, $removed] = pendingAdminPage();

    $this->actingWithPermissions([
        CmsPermission::PagesEdit->value,
    ]);

    $response = $this->get(route('cms.pages.edit', $page), $this->inertiaHeaders())
        ->assertOk()
        ->assertJsonPath('props.pendingRevision.diff.fields.0', [
            'key' => 'title',
            'label' => __('vue-cms::pages.fields.title'),
            'before' => 'Live',
            'after' => 'Nieuw',
            'words' => [
                ['type' => 'removed', 'text' => 'Live'],
                ['type' => 'added', 'text' => 'Nieuw'],
            ],
        ])
        ->assertJsonPath('props.pendingRevision.diff.fields.1.key', 'meta_description')
        ->assertJsonPath('props.pendingRevision.diff.fields.1.before', null)
        ->assertJsonPath('props.pendingRevision.diff.fields.1.after', 'Beschrijving');

    $blocks = $response->json('props.pendingRevision.diff.blocks');

    expect(array_map(fn (array $block): array => [$block['id'], $block['status']], $blocks))->toBe([
        [$kept->id, 'unchanged'],
        [$changed->id, 'changed'],
        [$removed->id, 'removed'],
        [-1, 'added'],
    ])
        ->and($blocks[1]['label'])->toBe(__('cms::blocks.rich_text.label'))
        ->and($blocks[1]['changes'])->toBe([
            [
                'key' => 'html',
                'label' => __('cms::blocks.rich_text.fields.html'),
                'before' => 'Oud',
                'after' => 'Nieuw',
                'words' => [
                    ['type' => 'removed', 'text' => 'Oud'],
                    ['type' => 'added', 'text' => 'Nieuw'],
                ],
            ],
        ])
        ->and($blocks[3]['changes'][0]['after'])->toBe('Erbij');
});

test('the diff labels nested block values with the labels of the block form', function (): void {
    $page = makePage([
        'title' => 'Live',
    ], PageStatus::Published);
    $registry = app(BlockRegistry::class);
    $tiles = app(SaveBlock::class)->handle($page, new Block, $registry->find('link_tiles'), [
        'tiles' => [
            ['name' => 'Bakker', 'url' => 'https://bakker.example'],
        ],
    ], null);

    app(PendingRevisionEditor::class)->updateBlock($page, $tiles->id, $registry->find('link_tiles'), [
        'tiles' => [
            ['name' => 'Slager', 'url' => 'https://bakker.example'],
        ],
    ], null);

    $this->actingWithPermissions([
        CmsPermission::PagesEdit->value,
    ]);

    $this->get(route('cms.pages.edit', $page), $this->inertiaHeaders())
        ->assertOk()
        ->assertJsonPath('props.pendingRevision.diff.blocks.0.changes.0.key', 'tiles.0.name')
        ->assertJsonPath('props.pendingRevision.diff.blocks.0.changes.0.label', sprintf(
            '%s › 1 › %s',
            __('cms::blocks.link_tiles.fields.tiles'),
            __('cms::blocks.link_tiles.fields.name'),
        ));
});

test('the page editor links to a signed noindex preview of the pending revision', function (): void {
    [$page] = pendingAdminPage();

    $this->actingWithPermissions([
        CmsPermission::PagesEdit->value,
    ]);

    $previewUrl = $this->get(route('cms.pages.edit', $page), $this->inertiaHeaders())
        ->assertOk()
        ->json('props.pendingRevision.previewUrl');

    expect($previewUrl)->toContain(sprintf('/cms/preview/%d/pending', $page->id))
        ->toContain('signature=');

    $this->get($previewUrl)
        ->assertOk()
        ->assertSee('Erbij', false)
        ->assertSee('Nieuw', false)
        ->assertDontSee('Weg', false)
        ->assertHeader('X-Robots-Tag', 'noindex, nofollow');

    $this->get(sprintf('/cms/preview/%d/pending', $page->id))->assertForbidden();
});

test('without the edit permission there is no page editor, so no diff and no preview link', function (): void {
    [$page] = pendingAdminPage();

    $this->actingWithPermissions([
        CmsPermission::PagesPublish->value,
    ]);

    $this->get(route('cms.pages.edit', $page), $this->inertiaHeaders())->assertForbidden();
});
