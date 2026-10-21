<?php

declare(strict_types=1);

namespace J87NL\VueCms\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use J87NL\Cms\Actions\DeleteMenuItem;
use J87NL\Cms\Actions\SaveMenuItem;
use J87NL\Cms\Models\Menu;
use J87NL\Cms\Models\MenuItem;
use J87NL\VueCms\Forms\MenuItemForm;
use TranquilTools\FormBuilder\FormConfig;

class MenuItemController extends Controller
{
    public function create(Menu $menu): Response
    {
        return $this->form($menu, __('vue-cms::menus.create.title'), MenuItemForm::make($menu)
            ->action(route('cms.menu-items.store', $menu))
            ->method('POST'));
    }

    public function store(Request $request, Menu $menu, SaveMenuItem $saveMenuItem): RedirectResponse
    {
        $this->save($menu, new MenuItem, MenuItemForm::make($menu)->validate($request), $saveMenuItem);

        $this->toast(__('vue-cms::menus.flash.saved'));

        return to_route('cms.menus.index');
    }

    public function edit(Menu $menu, MenuItem $item): Response
    {
        return $this->form($menu, __('vue-cms::menus.edit.title'), MenuItemForm::make($menu, $item)
            ->action(route('cms.menu-items.update', [$menu, $item]))
            ->method('PUT')
            ->fill([
                ...$item->only([
                    'page_id',
                    'url',
                    'label',
                    'parent_id',
                ]),
                'link_type' => is_null($item->page_id) ? MenuItemForm::LINK_URL : MenuItemForm::LINK_PAGE,
            ]));
    }

    public function update(Request $request, Menu $menu, MenuItem $item, SaveMenuItem $saveMenuItem): RedirectResponse
    {
        $this->save($menu, $item, MenuItemForm::make($menu, $item)->validate($request), $saveMenuItem);

        $this->toast(__('vue-cms::menus.flash.saved'));

        return to_route('cms.menus.index');
    }

    public function destroy(Menu $menu, MenuItem $item, DeleteMenuItem $deleteMenuItem): RedirectResponse
    {
        $deleteMenuItem->handle($menu, $item);

        $this->toast(__('vue-cms::menus.flash.deleted'));

        return to_route('cms.menus.index');
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function save(Menu $menu, MenuItem $item, array $validated, SaveMenuItem $saveMenuItem): void
    {
        $linksToPage = ($validated['link_type'] ?? null) === MenuItemForm::LINK_PAGE;
        $label = $validated['label'] ?? null;
        $parentId = $validated['parent_id'] ?? null;

        $saveMenuItem->handle(
            $menu,
            $item,
            $linksToPage ? (int) $validated['page_id'] : null,
            $linksToPage ? null : (string) $validated['url'],
            is_null($label) ? null : (string) $label,
            is_null($parentId) ? null : (int) $parentId,
        );
    }

    private function form(Menu $menu, string $title, FormConfig $form): Response
    {
        return Inertia::render($this->page('form'), [
            'title' => $title,
            'description' => $menu->label(),
            'breadcrumbs' => [
                [
                    'title' => __('vue-cms::menus.index.title'),
                    'href' => route('cms.menus.index'),
                ],
                [
                    'title' => $title,
                    'href' => route('cms.menus.index'),
                ],
            ],
            'backUrl' => route('cms.menus.index'),
            'form' => $form,
        ]);
    }
}
