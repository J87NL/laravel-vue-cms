<?php

declare(strict_types=1);

namespace J87NL\VueCms\Forms;

use Illuminate\Validation\Rule;
use J87NL\Cms\Enums\EntityType;
use J87NL\Cms\Media\MediaOptions;
use J87NL\Cms\Models\Page;
use J87NL\VueCms\Support\PageOptions;
use TranquilTools\FormBuilder\AbstractForm;
use TranquilTools\FormBuilder\Fields\BaseField;
use TranquilTools\FormBuilder\Fields\Email;
use TranquilTools\FormBuilder\Fields\Section;
use TranquilTools\FormBuilder\Fields\Select;
use TranquilTools\FormBuilder\Fields\Submit;
use TranquilTools\FormBuilder\Fields\Text;
use TranquilTools\FormBuilder\Fields\Textarea;
use TranquilTools\FormBuilder\FormConfig;

class SettingsForm extends AbstractForm
{
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
            Section::make()
                ->title(__('vue-cms::settings.sections.site'))
                ->columns(2)
                ->fields([
                    Text::make('site_name')
                        ->label(__('vue-cms::settings.fields.site_name'))
                        ->required()
                        ->rules([
                            'string',
                            'max:120',
                        ]),

                    Text::make('title_pattern')
                        ->label(__('vue-cms::settings.fields.title_pattern'))
                        ->help(__('vue-cms::settings.help.title_pattern'))
                        ->rules([
                            'nullable',
                            'string',
                            'max:160',
                        ]),

                    Textarea::make('default_meta_description')
                        ->label(__('vue-cms::settings.fields.default_meta_description'))
                        ->help(__('vue-cms::settings.help.default_meta_description'))
                        ->columnSpanFull()
                        ->rules([
                            'nullable',
                            'string',
                            'max:320',
                        ]),

                    $this->image('og_image_id')
                        ->help(__('vue-cms::settings.help.og_image_id')),

                    Select::make('privacy_page_id')
                        ->label(__('vue-cms::settings.fields.privacy_page_id'))
                        ->help(__('vue-cms::settings.help.privacy_page_id'))
                        ->options(PageOptions::all())
                        ->optionLabel('label')
                        ->optionValue('value')
                        ->searchable()
                        ->rules([
                            'nullable',
                            'integer',
                            Rule::exists((new Page)->getTable(), 'id'),
                        ]),

                    Email::make('contact_recipient')
                        ->label(__('vue-cms::settings.fields.contact_recipient'))
                        ->help(__('vue-cms::settings.help.contact_recipient'))
                        ->rules([
                            'nullable',
                            'email',
                            'max:254',
                        ]),
                ]),

            Section::make()
                ->title(__('vue-cms::settings.sections.entity'))
                ->description(__('vue-cms::settings.help.entity'))
                ->columns(2)
                ->fields([
                    Select::make('entity_type')
                        ->label(__('vue-cms::settings.fields.entity_type'))
                        ->options(array_map(
                            fn (EntityType $type): array => [
                                'value' => $type->value,
                                'label' => $type->label(),
                            ],
                            EntityType::cases(),
                        ))
                        ->optionLabel('label')
                        ->optionValue('value')
                        ->required()
                        ->rules([
                            Rule::enum(EntityType::class),
                        ]),

                    Text::make('entity_name')
                        ->label(__('vue-cms::settings.fields.entity_name'))
                        ->rules([
                            'nullable',
                            'string',
                            'max:120',
                        ]),

                    Text::make('entity_job_title')
                        ->label(__('vue-cms::settings.fields.entity_job_title'))
                        ->help(__('vue-cms::settings.help.entity_job_title'))
                        ->rules([
                            'nullable',
                            'string',
                            'max:120',
                        ]),

                    Email::make('entity_email')
                        ->label(__('vue-cms::settings.fields.entity_email'))
                        ->help(__('vue-cms::settings.help.entity_email'))
                        ->rules([
                            'nullable',
                            'email',
                            'max:254',
                        ]),

                    $this->image('entity_image_id'),

                    Textarea::make('entity_same_as')
                        ->label(__('vue-cms::settings.fields.entity_same_as'))
                        ->help(__('vue-cms::settings.help.entity_same_as'))
                        ->columnSpanFull()
                        ->rules([
                            'nullable',
                            'string',
                            'max:4000',
                        ]),
                ]),

            Section::make()
                ->title(__('vue-cms::settings.sections.findability'))
                ->fields([
                    Textarea::make('robots_extra')
                        ->label(__('vue-cms::settings.fields.robots_extra'))
                        ->help(__('vue-cms::settings.help.robots_extra'))
                        ->rules([
                            'nullable',
                            'string',
                            'max:4000',
                        ]),

                    Textarea::make('llms_intro')
                        ->label(__('vue-cms::settings.fields.llms_intro'))
                        ->help(__('vue-cms::settings.help.llms_intro'))
                        ->rules([
                            'nullable',
                            'string',
                            'max:4000',
                        ]),
                ]),

            Submit::make()->label(__('vue-cms::settings.actions.save')),
        ];
    }

    private function image(string $name): Select
    {
        return Select::make($name)
            ->label(__(sprintf('vue-cms::settings.fields.%s', $name)))
            ->options(MediaOptions::all())
            ->optionLabel('label')
            ->optionValue('value')
            ->searchable()
            ->rules([
                'nullable',
                'integer',
                MediaOptions::existsRule(),
            ]);
    }
}
