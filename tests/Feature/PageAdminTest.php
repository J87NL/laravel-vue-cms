<?php

declare(strict_types=1);

use J87NL\Cms\CmsPermission;
use J87NL\Cms\Enums\PageStatus;
use J87NL\Cms\Models\Block;
use J87NL\Cms\Models\Page;
use J87NL\Cms\Models\Revision;

beforeEach(function (): void {
    $this->actingWithPermissions([CmsPermission::PagesEdit->value]);
});

test('a new page starts as a draft with a slug from its title', function (): void {
    $this->post(route('cms.pages.store'), [
        'title' => 'Over mij',
    ])->assertRedirect();

    $page = Page::query()
        ->firstOrFail();

    expect($page->path)->toBe('over-mij')
        ->and($page->status)->toBe(PageStatus::Draft)
        ->and(Revision::query()
            ->where('page_id', $page->id)->count())->toBe(1);
});

test('the tree move endpoint changes the parent and recomputes paths', function (): void {
    $parent = makePage([
        'slug' => 'parent',
    ]);
    $child = makePage([
        'slug' => 'child',
    ]);
    $grandchild = makePage([
        'slug' => 'grandchild',
        'parent_id' => $child->id,
    ]);

    $this->post(route('cms.pages.move', $child), [
        'target' => $parent->id,
        'position' => 'inside',
    ])->assertRedirect(route('cms.pages.index'));

    expect($grandchild->fresh()->path)->toBe('parent/child/grandchild');
});

test('a block is added through the form of its type', function (): void {
    $page = makePage();

    $this->post(route('cms.blocks.store', [$page, 'rich_text']), [
        'html' => '<p onclick="x()">Hello</p><script>x()</script>',
    ])->assertRedirect(route('cms.pages.edit', $page));

    expect(Block::query()
        ->sole()->data)->toBe([
            'html' => '<p>Hello</p>',
        ]);
});

test('an embed outside the allowlist is rejected on save', function (): void {
    $page = makePage();

    $this->from(route('cms.blocks.create', [$page, 'embed']))
        ->post(route('cms.blocks.store', [$page, 'embed']), [
            'url' => 'https://www.youtube.com/embed/abc',
            'title' => 'Video',
        ])
        ->assertSessionHasErrors('url');

    expect(Block::query()
        ->count())->toBe(0);
});

test('an unknown or disabled block type is not found', function (): void {
    config()->set('cms.blocks.disabled', ['gallery']);

    $page = makePage();

    $this->get(route('cms.blocks.create', [$page, 'gallery']))->assertNotFound();
    $this->get(route('cms.blocks.create', [$page, 'nope']))->assertNotFound();
});

test('blocks can be hidden and deleted', function (): void {
    $page = makePage();

    $this->post(route('cms.blocks.store', [$page, 'rich_text']), [
        'html' => '<p>Hello</p>',
    ]);

    $block = Block::query()
        ->sole();

    $this->post(route('cms.blocks.toggle', [$page, $block]));

    expect($block->fresh()->is_visible)->toBeFalse();

    $this->delete(route('cms.blocks.destroy', [$page, $block]));

    expect(Block::query()
        ->count())->toBe(0);
});

test('a block of another page cannot be reached through this page', function (): void {
    $page = makePage([
        'slug' => 'first',
    ]);
    $other = makePage([
        'slug' => 'second',
    ]);

    $this->post(route('cms.blocks.store', [$other, 'rich_text']), [
        'html' => '<p>Other</p>',
    ]);

    $block = Block::query()
        ->sole();

    $this->delete(route('cms.blocks.destroy', [$page, $block]))->assertNotFound();
});

test('a revision is restored from the admin', function (): void {
    $page = makePage([
        'title' => 'Original',
        'slug' => 'original',
    ]);
    $revision = $page->revisions()->firstOrFail();

    $this->put(route('cms.pages.update', $page), [
        'title' => 'Changed',
        'slug' => 'changed',
    ]);

    $this->post(route('cms.revisions.restore', [$page, $revision]))
        ->assertRedirect(route('cms.pages.edit', $page));

    expect($page->fresh()->title)->toBe('Original')
        ->and($page->fresh()->path)->toBe('original');
});

test('a revision of another page cannot be restored through this page', function (): void {
    $page = makePage([
        'slug' => 'first',
    ]);
    $other = makePage([
        'slug' => 'second',
    ]);

    $this->post(route('cms.revisions.restore', [$page, $other->revisions()->firstOrFail()]))->assertNotFound();
});

test('the editor tells how many revisions are kept', function (): void {
    config()->set('cms.revisions.keep', 7);

    $page = makePage();

    $this->get(route('cms.pages.edit', $page), $this->inertiaHeaders())
        ->assertOk()
        ->assertJsonPath('props.revisionsKept', 7);
});

test('a page only takes a schema type from the configured list', function (): void {
    config()->set('cms.schema_types', [
        'WebPage',
        'AboutPage',
    ]);

    $page = makePage([
        'title' => 'About',
        'slug' => 'about',
    ]);

    $this->put(route('cms.pages.update', $page), [
        'title' => 'About',
        'slug' => 'about',
        'schema_type' => 'FAQPage',
    ])->assertSessionHasErrors('schema_type');

    $this->put(route('cms.pages.update', $page), [
        'title' => 'About',
        'slug' => 'about',
        'schema_type' => 'AboutPage',
    ])->assertSessionHasNoErrors();

    expect($page->fresh()->schema_type)->toBe('AboutPage');
});
