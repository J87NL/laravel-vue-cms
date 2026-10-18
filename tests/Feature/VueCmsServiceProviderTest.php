<?php

declare(strict_types=1);

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Gate;
use J87NL\Cms\CmsPermission;

test('the provider registers a navigation item guarded by the edit permission', function (): void {
    $item = collect(config('j87nl.navigation'))->firstWhere('key', 'cms.dashboard');

    expect($item)
        ->route->toBe('cms.dashboard')
        ->permission->toBe(CmsPermission::PagesEdit->value);
});

test('guests cannot open the cms dashboard', function (): void {
    $this->getJson(route('cms.dashboard'))->assertUnauthorized();
});

test('users without the edit permission get a forbidden response', function (): void {
    $this->actingAs(new User)
        ->getJson(route('cms.dashboard'))
        ->assertForbidden();
});

test('users with the edit permission see the configured dashboard page', function (): void {
    Gate::define(CmsPermission::PagesEdit->value, fn (): bool => true);

    config()->set('vue-cms.pages.dashboard', 'admin/CustomCmsDashboard');

    $this->actingAs(new User)
        ->get(route('cms.dashboard'), [
            'X-Inertia' => 'true',
        ])
        ->assertOk()
        ->assertJsonPath('component', 'admin/CustomCmsDashboard');
});
