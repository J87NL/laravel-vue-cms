<?php

declare(strict_types=1);

use J87NL\Cms\CmsPermission;

test('a publisher schedules a draft and sees the preview link', function (): void {
    $page = makePage([
        'title' => 'Soon',
        'slug' => 'soon',
    ]);

    $this->actingWithPermissions([
        CmsPermission::PagesEdit->value,
        CmsPermission::PagesPublish->value,
    ]);

    $publishAt = now()->addDay()->startOfMinute();

    $this->post(route('cms.pages.schedule', $page), [
        'publish_at' => $publishAt->format('Y-m-d H:i:s'),
    ])->assertRedirect(route('cms.pages.edit', $page));

    expect($page->fresh()->publish_at?->equalTo($publishAt))->toBeTrue();

    $response = $this->get(route('cms.pages.edit', $page), $this->inertiaHeaders())
        ->assertOk()
        ->assertJsonPath('props.publication.publishAt', $publishAt->toIso8601String());

    $previewUrl = (string) $response->json('props.previewUrl');

    expect($previewUrl)->toContain('signature=');

    $this->get($previewUrl)->assertOk()->assertHeader('X-Robots-Tag', 'noindex, nofollow');
});

test('scheduling needs the publish permission', function (): void {
    $page = makePage();

    $this->actingWithPermissions([CmsPermission::PagesEdit->value]);

    $this->get(route('cms.pages.edit', $page), $this->inertiaHeaders())
        ->assertJsonPath('props.publication.scheduleForm', null);

    $this->post(route('cms.pages.schedule', $page), [
        'publish_at' => now()->addDay()->format('Y-m-d H:i:s'),
    ])->assertForbidden();
});

test('a schedule in the past is refused', function (): void {
    $page = makePage();

    $this->actingWithPermissions([
        CmsPermission::PagesEdit->value,
        CmsPermission::PagesPublish->value,
    ]);

    $this->post(route('cms.pages.schedule', $page), [
        'publish_at' => now()->subDay()->format('Y-m-d H:i:s'),
    ])->assertSessionHasErrors('publish_at');
});

test('the editor shows why a scheduled change was refused', function (): void {
    $page = makePage();
    $page->schedule_error = 'The home page cannot go offline.';
    $page->save();

    $this->actingWithPermissions([CmsPermission::PagesEdit->value]);

    $this->get(route('cms.pages.edit', $page), $this->inertiaHeaders())
        ->assertOk()
        ->assertJsonPath('props.publication.scheduleError', 'The home page cannot go offline.');
});
