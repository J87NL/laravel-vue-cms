<?php

declare(strict_types=1);

use J87NL\Cms\Actions\SaveBlock;
use J87NL\Cms\Blocks\BlockRegistry;
use J87NL\Cms\CmsPermission;
use J87NL\Cms\Enums\PageStatus;
use J87NL\Cms\Models\Block;
use J87NL\Cms\Models\Page;
use J87NL\Cms\Models\Revision;
use J87NL\Cms\Pages\PendingRevisionEditor;

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
