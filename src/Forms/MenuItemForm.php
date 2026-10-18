<?php

declare(strict_types=1);

namespace J87NL\VueCms\Forms;

use Illuminate\Validation\Rule;
use J87NL\Cms\Models\Menu;
use J87NL\Cms\Models\MenuItem;
use J87NL\Cms\Models\Page;
use J87NL\Cms\Rules\SafeLink;
use J87NL\VueCms\Support\PageOptions;
use TranquilTools\FormBuilder\AbstractForm;
use TranquilTools\FormBuilder\Fields\BaseField;
use TranquilTools\FormBuilder\Fields\Radio;
use TranquilTools\FormBuilder\Fields\Select;
use TranquilTools\FormBuilder\Fields\Submit;
use TranquilTools\FormBuilder\Fields\Text;
use TranquilTools\FormBuilder\FormConfig;

class MenuItemForm extends AbstractForm
{
    public const string LINK_PAGE = 'page';

    public const string LINK_URL = 'url';

    public function __construct(
        private readonly Menu $menu,
        private readonly ?MenuItem $item = null,
    ) {}

    public function configure(FormConfig $form): void
    {
        $form->class('space-y-6');
    }

    /**
     * @return list<BaseField>
     */
    public function fields(): array
    {
        return [
            Radio::make('link_type')
                ->label(__('vue-cms::menus.fields.link_type'))
                ->options([
                    [
                        'value' => self::LINK_PAGE,
                        'label' => __('vue-cms::menus.link_types.page'),
                    ],
                    [
                        'value' => self::LINK_URL,
                        'label' => __('vue-cms::menus.link_types.url'),
                    ],
                ])
                ->optionLabel('label')
                ->optionValue('value')
                ->inline()
                ->default(self::LINK_PAGE)
                ->required()
                ->rules([
                    Rule::in([
                        self::LINK_PAGE,
                        self::LINK_URL,
                    ]),
                ]),

            Select::make('page_id')
                ->label(__('vue-cms::menus.fields.page_id'))
                ->options(PageOptions::all())
                ->optionLabel('label')
                ->optionValue('value')
                ->searchable()
                ->if(sprintf("link_type === '%s'", self::LINK_PAGE))
                ->rules([
                    sprintf('required_if:link_type,%s', self::LINK_PAGE),
                    'nullable',
                    'integer',
                    Rule::exists((new Page)->getTable(), 'id'),
                ]),

            Text::make('url')
                ->label(__('vue-cms::menus.fields.url'))
                ->help(__('vue-cms::menus.help.url'))
                ->if(sprintf("link_type === '%s'", self::LINK_URL))
                ->rules([
                    sprintf('required_if:link_type,%s', self::LINK_URL),
                    'nullable',
                    'string',
                    'max:2048',
                    new SafeLink,
                ]),

            Text::make('label')
                ->label(__('vue-cms::menus.fields.label'))
                ->help(__('vue-cms::menus.help.label'))
                ->rules([
                    sprintf('required_if:link_type,%s', self::LINK_URL),
                    'nullable',
                    'string',
                    'max:80',
                ]),

            Select::make('parent_id')
                ->label(__('vue-cms::menus.fields.parent_id'))
                ->options($this->parentOptions())
                ->optionLabel('label')
                ->optionValue('value')
                ->rules([
                    'nullable',
                    'integer',
                    Rule::in(array_column($this->parentOptions(), 'value')),
                ]),

            Submit::make()->label(__('vue-cms::menus.actions.save')),
        ];
    }

    /**
     * @return list<array{value: int, label: string}>
     */
    private function parentOptions(): array
    {
        return array_values(MenuItem::query()
            ->with('page')
            ->where('menu_id', $this->menu->id)
            ->whereNull('parent_id')
            ->when(! is_null($this->item), fn ($query) => $query->whereKeyNot($this->item?->id))
            ->orderBy('sort_order')
            ->get()
            ->map(fn (MenuItem $item): array => [
                'value' => $item->id,
                'label' => $item->displayLabel(),
            ])
            ->all());
    }
}
