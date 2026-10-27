<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use J87NL\Cms\CmsPermission;
use J87NL\Cms\Models\SiteSettings;

test('the spam protection screen needs the settings permission', function (): void {
    $this->actingWithPermissions([CmsPermission::PagesEdit->value]);

    $this->get(route('cms.spam-protection.edit'))->assertForbidden();
    $this->put(route('cms.spam-protection.update'), [
        'contact_turnstile_site_key' => 'site-key',
    ])->assertForbidden();

    expect(SiteSettings::current()->contact_turnstile_site_key)->toBeNull();
});

test('the website settings no longer hold the turnstile fields', function (): void {
    $this->actingWithPermissions([CmsPermission::Settings->value]);

    $form = $this->get(route('cms.settings.edit'), $this->inertiaHeaders())->json('props.form');

    expect(json_encode($form))->not->toContain('turnstile');
});

test('the turnstile secret key is saved encrypted, never sent back, kept when left empty and removable', function (): void {
    $this->actingWithPermissions([CmsPermission::Settings->value]);

    $page = $this->get(route('cms.spam-protection.edit'), $this->inertiaHeaders())
        ->assertOk()
        ->json('props');

    expect($page['notice'])->toBe(__('vue-cms::spam_protection.status.inactive'))
        ->and(json_encode($page['form']))
        ->not->toContain('"stored":true')
        ->not->toContain(SiteSettings::REMOVE_TURNSTILE_SECRET);

    $this->put(route('cms.spam-protection.update'), [
        'contact_turnstile_site_key' => ' site-key ',
        'contact_turnstile_secret_key' => 'top-secret',
    ])->assertRedirect(route('cms.spam-protection.edit'));

    expect(SiteSettings::current())
        ->contact_turnstile_site_key->toBe('site-key')
        ->contact_turnstile_secret_key->toBe('top-secret')
        ->and(DB::table('cms_settings')->value('contact_turnstile_secret_key'))->not->toContain('top-secret');

    $response = $this->get(route('cms.spam-protection.edit'), $this->inertiaHeaders())->assertOk();

    expect($response->getContent())->not->toContain('top-secret')
        ->and($response->json('props.notice'))->toBe(__('vue-cms::spam_protection.status.active'))
        ->and(json_encode($response->json('props.form')))
        ->toContain('"stored":true')
        ->toContain(SiteSettings::REMOVE_TURNSTILE_SECRET)
        ->toContain('site-key');

    $this->put(route('cms.spam-protection.update'), [
        'contact_turnstile_site_key' => 'site-key',
        'contact_turnstile_secret_key' => '',
    ])->assertRedirect(route('cms.spam-protection.edit'));

    expect(SiteSettings::current()->contact_turnstile_secret_key)->toBe('top-secret');

    $this->put(route('cms.spam-protection.update'), [
        'contact_turnstile_site_key' => 'site-key',
        'contact_turnstile_secret_key' => '',
        SiteSettings::REMOVE_TURNSTILE_SECRET => '1',
    ])->assertRedirect(route('cms.spam-protection.edit'));

    expect(SiteSettings::current()->hasTurnstileSecret())->toBeFalse();

    $this->get(route('cms.spam-protection.edit'), $this->inertiaHeaders())
        ->assertJsonPath('props.notice', __('vue-cms::spam_protection.status.inactive'));
});

test('saving the website settings keeps the turnstile keys', function (): void {
    $this->actingWithPermissions([CmsPermission::Settings->value]);

    $this->put(route('cms.spam-protection.update'), [
        'contact_turnstile_site_key' => 'site-key',
        'contact_turnstile_secret_key' => 'top-secret',
    ]);

    $this->put(route('cms.settings.update'), [
        'site_name' => 'Johan',
        'entity_type' => 'Person',
    ])->assertRedirect(route('cms.settings.edit'));

    expect(SiteSettings::current())
        ->contact_turnstile_site_key->toBe('site-key')
        ->contact_turnstile_secret_key->toBe('top-secret');
});
