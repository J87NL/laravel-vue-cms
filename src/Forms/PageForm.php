<?php

declare(strict_types=1);

namespace J87NL\VueCms\Forms;

use Illuminate\Validation\Rule;
use J87NL\Cms\Media\MediaOptions;
use J87NL\Cms\Models\Page;
use J87NL\Cms\Validation\PageRules;
use J87NL\VueCms\Support\PageOptions;
use TranquilTools\FormBuilder\AbstractForm;
use TranquilTools\FormBuilder\Fields\BaseField;
use TranquilTools\FormBuilder\Fields\Section;
use TranquilTools\FormBuilder\Fields\Select;
use TranquilTools\FormBuilder\Fields\Submit;
use TranquilTools\FormBuilder\Fields\Text;
use TranquilTools\FormBuilder\Fields\Textarea;
use TranquilTools\FormBuilder\Fields\Toggle;
use TranquilTools\FormBuilder\FormConfig;

class PageForm extends AbstractForm
{
    public function __construct(
        private readonly ?Page $page = null,
        private readonly bool $disabled = false,
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
        $sections = $this->sections();

        if (! $this->disabled) {
            return [
                ...$sections,

                Submit::make()->label(__('vue-cms::pages.actions.save')),
            ];
        }

        foreach ($sections as $section) {
            foreach ($section->getFields() as $field) {
                $field->disabled();
            }
        }

        return $sections;
    }

    /**
     * @return list<Section>
     */
    private function sections(): array
    {
        return [
            Section::make()
                ->title(__('vue-cms::pages.sections.page'))
                ->columns(2)
                ->fields([
                    Text::make('title')
                        ->label(__('vue-cms::pages.fields.title'))
                        ->required()
                        ->rules(PageRules::for('title')),

                    Text::make('slug')
                        ->label(__('vue-cms::pages.fields.slug'))
                        ->help(__('vue-cms::pages.help.slug'))
                        ->rules(PageRules::for('slug')),

                    Select::make('parent_id')
                        ->label(__('vue-cms::pages.fields.parent_id'))
                        ->options(PageOptions::all($this->page))
                        ->optionLabel('label')
                        ->optionValue('value')
                        ->searchable()
                        ->rules(PageRules::for('parent_id')),

                    Text::make('menu_title')
                        ->label(__('vue-cms::pages.fields.menu_title'))
                        ->help(__('vue-cms::pages.help.menu_title'))
                        ->rules(PageRules::for('menu_title')),
                ]),

            Section::make()
                ->title(__('vue-cms::pages.sections.seo'))
                ->description(__('vue-cms::pages.help.seo'))
                ->columns(2)
                ->fields([
                    Text::make('meta_title')
                        ->label(__('vue-cms::pages.fields.meta_title'))
                        ->rules(PageRules::for('meta_title')),

                    Select::make('og_image_id')
                        ->label(__('vue-cms::pages.fields.og_image_id'))
                        ->options(MediaOptions::all())
                        ->optionLabel('label')
                        ->optionValue('value')
                        ->searchable()
                        ->rules(PageRules::for('og_image_id')),

                    Textarea::make('meta_description')
                        ->label(__('vue-cms::pages.fields.meta_description'))
                        ->columnSpanFull()
                        ->rules(PageRules::for('meta_description')),

                    Text::make('canonical_url')
                        ->label(__('vue-cms::pages.fields.canonical_url'))
                        ->rules(PageRules::for('canonical_url')),

                    Select::make('schema_type')
                        ->label(__('vue-cms::pages.fields.schema_type'))
                        ->options($this->schemaTypes())
                        ->optionLabel('label')
                        ->optionValue('value')
                        ->rules([
                            'nullable',
                            Rule::in(array_column($this->schemaTypes(), 'value')),
                        ]),

                    Toggle::make('noindex')
                        ->label(__('vue-cms::pages.fields.noindex'))
                        ->help(__('vue-cms::pages.help.noindex'))
                        ->rules(PageRules::for('noindex')),
                ]),
        ];
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    private function schemaTypes(): array
    {
        $types = config('vue-cms.schema_types', []);

        if (! is_array($types)) {
            return [];
        }

        return array_values(array_map(
            fn (mixed $type): array => [
                'value' => (string) $type,
                'label' => (string) $type,
            ],
            $types,
        ));
    }
}
