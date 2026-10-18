<?php

declare(strict_types=1);

namespace J87NL\VueCms\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
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

    public function store(Request $request, Menu $menu): RedirectResponse
    {
        $item = new MenuItem;
        $item->menu()->associate($menu);
        $item->sort_order = $this->nextSortOrder($menu);

        $this->save($item, MenuItemForm::make($menu)->validate($request));

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

    public function update(Request $request, Menu $menu, MenuItem $item): RedirectResponse
    {
        $validated = MenuItemForm::make($menu, $item)->validate($request);

        $hasChildren = MenuItem::query()
            ->where('parent_id', $item->id)
            ->exists();

        if ($hasChildren && ! is_null($validated['parent_id'] ?? null)) {
            throw ValidationException::withMessages([
                'parent_id' => __('vue-cms::menus.errors.has_children'),
            ]);
        }

        $this->save($item, $validated);

        $this->toast(__('vue-cms::menus.flash.saved'));

        return to_route('cms.menus.index');
    }

    public function destroy(Menu $menu, MenuItem $item): RedirectResponse
    {
        $item->delete();

        $this->toast(__('vue-cms::menus.flash.deleted'));

        return to_route('cms.menus.index');
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function save(MenuItem $item, array $validated): void
    {
        $linksToPage = ($validated['link_type'] ?? null) === MenuItemForm::LINK_PAGE;

        $item->fill([
            'page_id' => $linksToPage ? $validated['page_id'] : null,
            'url' => $linksToPage ? null : $validated['url'],
            'label' => $validated['label'] ?? null,
            'parent_id' => $validated['parent_id'] ?? null,
        ]);

        $item->save();
    }

    private function nextSortOrder(Menu $menu): int
    {
        $max = MenuItem::query()
            ->where('menu_id', $menu->id)
            ->max('sort_order');

        return (is_numeric($max) ? (int) $max : 0) + 1;
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
