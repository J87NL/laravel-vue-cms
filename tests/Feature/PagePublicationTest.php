<?php

declare(strict_types=1);

use J87NL\Cms\Actions\MakeHomePage;
use J87NL\Cms\CmsPermission;
use J87NL\Cms\Enums\PageStatus;

test('an editor without the publish permission can edit but not publish', function (): void {
    $page = makePage([
        'title' => 'About',
        'slug' => 'about',
    ]);

    $this->actingWithPermissions([CmsPermission::PagesEdit->value]);

    $this->put(route('cms.pages.update', $page), [
        'title' => 'About me',
        'slug' => 'about',
    ])->assertRedirect(route('cms.pages.edit', $page));

    expect($page->fresh()->title)->toBe('About me');

    $this->get(route('cms.pages.edit', $page), $this->inertiaHeaders())
        ->assertOk()
        ->assertJsonPath('props.publication.canPublish', false);

    $this->post(route('cms.pages.publish', $page))->assertForbidden();
    $this->post(route('cms.pages.home', $page))->assertForbidden();

    expect($page->fresh()->status)->toBe(PageStatus::Draft);
});

test('an editor without the publish permission cannot unpublish or delete a published page', function (): void {
    $page = makePage([
        'slug' => 'live',
    ], PageStatus::Published);
    makePage([
        'slug' => 'other',
    ]);
    $page->update([
        'title' => 'Live',
    ]);
    $page->forceFill([
        'is_home' => false,
    ])->save();

    $this->actingWithPermissions([CmsPermission::PagesEdit->value]);

    $this->post(route('cms.pages.unpublish', $page))->assertForbidden();
    $this->delete(route('cms.pages.destroy', $page))->assertForbidden();

    $this->get(route('cms.pages.edit', $page), $this->inertiaHeaders())
        ->assertJsonPath('props.deletion.blockedReason', __('vue-cms::pages.publication.delete_requires_publish'));

    expect($page->fresh()->status)->toBe(PageStatus::Published);
});

test('a publisher can publish and unpublish a page', function (): void {
    $home = makePage([
        'slug' => 'home',
    ], PageStatus::Published);
    app(MakeHomePage::class)->handle($home);
    $page = makePage([
        'slug' => 'about',
    ]);

    $this->actingWithPermissions([
        CmsPermission::PagesEdit->value,
        CmsPermission::PagesPublish->value,
    ]);

    $this->get(route('cms.pages.edit', $page), $this->inertiaHeaders())
        ->assertJsonPath('props.publication.canPublish', true);

    $this->post(route('cms.pages.publish', $page))->assertRedirect(route('cms.pages.edit', $page));

    expect($page->fresh()->status)->toBe(PageStatus::Published);

    $this->post(route('cms.pages.unpublish', $page));

    expect($page->fresh()->status)->toBe(PageStatus::Unpublished);
});

test('only a published page can be made the home page', function (): void {
    makePage([
        'slug' => 'home',
    ], PageStatus::Published);
    $draft = makePage([
        'slug' => 'draft',
    ]);

    $this->actingWithPermissions([
        CmsPermission::PagesEdit->value,
        CmsPermission::PagesPublish->value,
    ]);

    $this->get(route('cms.pages.edit', $draft), $this->inertiaHeaders())
        ->assertJsonPath('props.publication.homeBlockedReason', __('cms::validation.home_not_public'));

    $this->post(route('cms.pages.home', $draft))->assertSessionHasErrors('page');

    expect($draft->fresh()->is_home)->toBeFalse();
});

test('the home page and its ancestors show why they cannot be taken offline', function (): void {
    $parent = makePage([
        'slug' => 'parent',
    ], PageStatus::Published);
    $home = makePage([
        'slug' => 'home',
        'parent_id' => $parent->id,
    ], PageStatus::Published);
    app(MakeHomePage::class)->handle($home);

    $this->actingWithPermissions([
        CmsPermission::PagesEdit->value,
        CmsPermission::PagesPublish->value,
    ]);

    $this->get(route('cms.pages.edit', $home), $this->inertiaHeaders())
        ->assertJsonPath('props.publication.unpublishBlockedReason', __('cms::validation.unpublish_home'));
    $this->get(route('cms.pages.edit', $parent), $this->inertiaHeaders())
        ->assertJsonPath('props.publication.unpublishBlockedReason', __('cms::validation.unpublish_home_ancestor'));

    $this->post(route('cms.pages.unpublish', $parent))->assertSessionHasErrors('page');

    expect($parent->fresh()->status)->toBe(PageStatus::Published);
});
