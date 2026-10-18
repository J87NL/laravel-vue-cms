<?php

declare(strict_types=1);

use J87NL\Cms\CmsPermission;

test('guests cannot open the cms admin', function (): void {
    $this->getJson(route('cms.pages.index'))->assertUnauthorized();
});

test('users without the matching permission get a forbidden response', function (string $route, string $permission): void {
    $this->actingWithPermissions(array_values(array_diff(CmsPermission::names(), [$permission])))
        ->getJson(route($route))
        ->assertForbidden();
})->with([
    ['cms.pages.index', CmsPermission::PagesEdit->value],
    ['cms.media.index', CmsPermission::Media->value],
    ['cms.menus.index', CmsPermission::Menus->value],
]);

test('the configured page component is rendered', function (): void {
    config()->set('vue-cms.pages.pages_index', 'admin/CustomPages');

    $this->actingWithPermissions([CmsPermission::PagesEdit->value])
        ->get(route('cms.pages.index'), $this->inertiaHeaders())
        ->assertOk()
        ->assertJsonPath('component', 'admin/CustomPages');
});
