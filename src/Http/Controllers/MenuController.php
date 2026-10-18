<?php

declare(strict_types=1);

namespace J87NL\VueCms\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use J87NL\Cms\Models\Menu;
use J87NL\VueCms\Tables\MenuItemTable;

class MenuController extends Controller
{
    public function index(): Response
    {
        $keys = config('cms.menus', []);

        $menus = array_map(function (mixed $key): array {
            $menu = Menu::forKey((string) $key);

            return [
                'key' => $menu->key,
                'label' => $menu->label(),
                'items' => MenuItemTable::build($menu),
                'createUrl' => route('cms.menu-items.create', $menu),
            ];
        }, is_array($keys) ? array_values($keys) : []);

        return Inertia::render($this->page('menus_index'), [
            'menus' => $menus,
            'breadcrumbs' => [
                [
                    'title' => __('vue-cms::menus.index.title'),
                    'href' => route('cms.menus.index'),
                ],
            ],
        ]);
    }
}
