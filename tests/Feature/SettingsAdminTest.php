<?php

declare(strict_types=1);

use J87NL\Cms\CmsPermission;
use J87NL\Cms\Enums\EntityType;
use J87NL\Cms\Models\SiteSettings;

test('the settings need the settings permission', function (): void {
    $this->actingWithPermissions([CmsPermission::PagesEdit->value]);

    $this->get(route('cms.settings.edit'))->assertForbidden();
    $this->put(route('cms.settings.update'), [])->assertForbidden();
});

test('an admin saves the site settings', function (): void {
    $this->actingWithPermissions([CmsPermission::Settings->value]);
    $privacy = makePage([
        'title' => 'Privacy',
    ]);

    $this->get(route('cms.settings.edit'), $this->inertiaHeaders())->assertOk();

    $this->put(route('cms.settings.update'), [
        'site_name' => 'Johan Montenij',
        'title_pattern' => ':title | Johan',
        'entity_type' => 'Organization',
        'entity_same_as' => "https://github.com/J87NL\nhttps://github.com/J87NL",
        'privacy_page_id' => $privacy->id,
        'contact_recipient' => 'contact@j87.nl',
        'robots_extra' => 'User-agent: GPTBot',
    ])->assertRedirect(route('cms.settings.edit'));

    expect(SiteSettings::current())
        ->siteName()->toBe('Johan Montenij')
        ->entity_type->toBe(EntityType::Organization)
        ->sameAs()->toBe(['https://github.com/J87NL'])
        ->privacy_page_id->toBe($privacy->id);

    $this->assertDatabaseCount('cms_settings', 1);
});

test('profile links must be full web addresses', function (): void {
    $this->actingWithPermissions([CmsPermission::Settings->value]);

    $this->put(route('cms.settings.update'), [
        'site_name' => 'Johan',
        'entity_type' => 'Person',
        'entity_same_as' => 'javascript:alert(1)',
    ])->assertSessionHasErrors('entity_same_as');
});

test('the back button only shows for users who may open the pages', function (array $permissions, bool $hasBackUrl): void {
    $this->actingWithPermissions($permissions);

    $backUrl = $this->get(route('cms.settings.edit'), $this->inertiaHeaders())
        ->assertOk()
        ->json('props.backUrl');

    expect(is_null($backUrl))->toBe(! $hasBackUrl);

    if ($hasBackUrl) {
        $this->get($backUrl, $this->inertiaHeaders())->assertOk();
    }
})->with([
    'settings only' => [[CmsPermission::Settings->value], false],
    'settings and pages' => [[CmsPermission::Settings->value, CmsPermission::PagesEdit->value], true],
]);
