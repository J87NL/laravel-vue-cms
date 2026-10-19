<?php

declare(strict_types=1);

use J87NL\Cms\CmsPermission;
use J87NL\Cms\Enums\PageStatus;
use J87NL\Cms\Models\Menu;
use J87NL\Cms\Models\MenuItem;

beforeEach(function (): void {
    $this->actingWithPermissions([CmsPermission::Menus->value]);
});

test('the menus screen creates the configured menus', function (): void {
    $this->get(route('cms.menus.index'), $this->inertiaHeaders())
        ->assertOk()
        ->assertJsonCount(2, 'props.menus');

    expect(Menu::query()
        ->orderBy('id')->pluck('key')->all())->toBe(['main', 'footer']);
});

test('a menu item links to a page or to a safe link', function (): void {
    $page = makePage();
    $menu = Menu::forKey('main');

    $this->post(route('cms.menu-items.store', $menu), [
        'link_type' => 'page',
        'page_id' => $page->id,
    ])->assertRedirect(route('cms.menus.index'));

    $this->post(route('cms.menu-items.store', $menu), [
        'link_type' => 'url',
        'url' => 'javascript:alert(1)',
        'label' => 'Bad',
    ])->assertSessionHasErrors('url');

    $this->post(route('cms.menu-items.store', $menu), [
        'link_type' => 'url',
        'url' => 'https://github.com/J87NL',
        'label' => 'GitHub',
    ]);

    expect(MenuItem::query()
        ->orderBy('sort_order')->get(['page_id', 'url'])->toArray())->toBe([
            [
                'page_id' => $page->id,
                'url' => null,
            ],
            [
                'page_id' => null,
                'url' => 'https://github.com/J87NL',
            ],
        ]);
});

test('an item of another menu cannot be changed through this menu', function (): void {
    $item = Menu::forKey('footer')->items()->create([
        'url' => '/contact',
        'label' => 'Contact',
    ]);

    $this->delete(route('cms.menu-items.destroy', [Menu::forKey('main'), $item]))->assertNotFound();

    expect(MenuItem::query()
        ->whereKey($item->id)->exists())->toBeTrue();
});

test('the visible column follows the public menu, including parent pages', function (): void {
    $parent = makePage([
        'slug' => 'parent',
    ]);
    $child = makePage([
        'slug' => 'child',
        'parent_id' => $parent->id,
    ], PageStatus::Published);

    Menu::forKey('main')
        ->items()
        ->create([
            'page_id' => $child->id,
        ]);

    $this->get(route('cms.menus.index'), $this->inertiaHeaders())
        ->assertJsonPath('props.menus.0.items.data.0.status', __('vue-cms::menus.statuses.hidden'));
});

test('an item with items below it cannot be placed under another item', function (): void {
    $menu = Menu::forKey('main');
    $parent = $menu->items()->create([
        'url' => '/a',
        'label' => 'A',
    ]);
    $menu->items()->create([
        'url' => '/b',
        'label' => 'B',
        'parent_id' => $parent->id,
    ]);
    $other = $menu->items()->create([
        'url' => '/c',
        'label' => 'C',
    ]);

    $this->put(route('cms.menu-items.update', [$menu, $parent]), [
        'link_type' => 'url',
        'url' => '/a',
        'label' => 'A',
        'parent_id' => $other->id,
    ])->assertSessionHasErrors([
        'parent_id' => __('cms::validation.menu_item_has_children'),
    ]);

    expect($parent->refresh()->parent_id)->toBeNull();
});

test('a new menu item goes at the end of its menu', function (): void {
    $menu = Menu::forKey('main');
    $menu->items()->create([
        'url' => '/a',
        'label' => 'A',
        'sort_order' => 7,
    ]);

    $this->post(route('cms.menu-items.store', $menu), [
        'link_type' => 'url',
        'url' => '/b',
        'label' => 'B',
    ])->assertRedirect(route('cms.menus.index'));

    expect(MenuItem::query()
        ->where('label', 'B')
        ->value('sort_order'))->toBe(8);
});
