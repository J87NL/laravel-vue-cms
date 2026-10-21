<?php

declare(strict_types=1);

namespace J87NL\VueCms\Tables;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use InvalidArgumentException;
use J87NL\Cms\Actions\ReorderMenuItems;
use J87NL\Cms\CmsPermission;
use J87NL\Cms\Models\Menu;
use J87NL\Cms\Models\MenuItem;
use J87NL\Cms\Models\Page;
use J87NL\Cms\Pages\PageVisibility;
use TranquilTools\TableBuilder\AbstractTable;
use TranquilTools\TableBuilder\TableBuilder;

final class MenuItemTable extends AbstractTable
{
    public function __construct(
        private readonly Menu $menu,
    ) {}

    public function authorize(Request $request): bool
    {
        return (bool) $request->user()?->can(CmsPermission::Menus->value);
    }

    /**
     * @return array<string, int>
     */
    public function signedParameters(): array
    {
        return [
            'menu' => $this->menu->id,
        ];
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public static function fromSignedParameters(array $parameters, Request $request): static
    {
        return new self(Menu::query()
            ->findOrFail((int) ($parameters['menu'] ?? 0)));
    }

    /**
     * @return Builder<MenuItem>
     */
    public function for(): Builder
    {
        return MenuItem::query()
            ->with('page')
            ->where('menu_id', $this->menu->id)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function configure(TableBuilder $table): void
    {
        $visibility = app(PageVisibility::class);
        $pagePaths = array_values(Page::query()
            ->whereIn('id', MenuItem::query()
                ->where('menu_id', $this->menu->id)
                ->whereNotNull('page_id')
                ->select('page_id'))
            ->pluck('path')
            ->map(fn (mixed $path): string => (string) $path)
            ->all());
        $hiddenAncestors = $visibility->hiddenAncestorPaths($pagePaths);
        $isVisible = fn (MenuItem $item): bool => $item->isPublic()
            && (is_null($item->page) || ! $visibility->isHiddenBy($item->page->path, $hiddenAncestors));

        $table
            ->name(sprintf('menu_%s', $this->menu->key))
            ->column('label', __('vue-cms::menus.fields.label'), as: fn (mixed $value, MenuItem $item): string => is_null($item->parent_id) ? $item->displayLabel() : sprintf('— %s', $item->displayLabel()))
            ->column('link', __('vue-cms::menus.fields.link'), as: fn (mixed $value, MenuItem $item): string => (string) $item->href())
            ->column('status', __('vue-cms::menus.fields.status'), as: fn (mixed $value, MenuItem $item): string => $isVisible($item) ? __('vue-cms::menus.statuses.visible') : __('vue-cms::menus.statuses.hidden'))
            ->reorderable('sort_order')
            ->rowAction(
                __('vue-cms::menus.actions.edit'),
                url: fn (MenuItem $item): string => route('cms.menu-items.edit', [$this->menu, $item]),
                key: 'edit',
            )
            ->rowAction(
                __('vue-cms::menus.actions.delete'),
                url: fn (MenuItem $item): string => route('cms.menu-items.destroy', [$this->menu, $item]),
                method: 'delete',
                confirm: __('vue-cms::menus.actions.delete_confirm'),
                variant: 'destructive',
                key: 'delete',
            );
    }

    public function performReorder(int|string $id, int|string $toId): void
    {
        if (! ctype_digit((string) $id) || ! ctype_digit((string) $toId)) {
            throw new InvalidArgumentException('Both menu items must be given by their id.');
        }

        app(ReorderMenuItems::class)->move($this->menu, (int) $id, (int) $toId);
    }
}
