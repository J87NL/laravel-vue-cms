<?php

declare(strict_types=1);

namespace J87NL\VueCms\Forms;

use TranquilTools\FormBuilder\AbstractForm;
use TranquilTools\FormBuilder\Fields\BaseField;
use TranquilTools\FormBuilder\Fields\Number;
use TranquilTools\FormBuilder\Fields\Section;
use TranquilTools\FormBuilder\Fields\Submit;
use TranquilTools\FormBuilder\Fields\Text;
use TranquilTools\FormBuilder\Fields\Textarea;
use TranquilTools\FormBuilder\FormConfig;

class MediaForm extends AbstractForm
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
            ...self::detailFields(),

            Submit::make()->label(__('vue-cms::media.actions.save')),
        ];
    }

    /**
     * @return list<BaseField>
     */
    public static function detailFields(): array
    {
        return [
            Text::make('alt')
                ->label(__('vue-cms::media.fields.alt'))
                ->help(__('vue-cms::media.help.alt'))
                ->required()
                ->rules([
                    'string',
                    'max:250',
                ]),

            Textarea::make('caption')
                ->label(__('vue-cms::media.fields.caption'))
                ->rules([
                    'nullable',
                    'string',
                    'max:500',
                ]),

            Section::make()
                ->title(__('vue-cms::media.sections.focal_point'))
                ->description(__('vue-cms::media.help.focal_point'))
                ->columns(2)
                ->fields([
                    Number::make('focal_x')
                        ->label(__('vue-cms::media.fields.focal_x'))
                        ->minValue(0)
                        ->maxValue(100)
                        ->append('%')
                        ->default(50)
                        ->required()
                        ->rules([
                            'integer',
                        ]),

                    Number::make('focal_y')
                        ->label(__('vue-cms::media.fields.focal_y'))
                        ->minValue(0)
                        ->maxValue(100)
                        ->append('%')
                        ->default(50)
                        ->required()
                        ->rules([
                            'integer',
                        ]),
                ]),
        ];
    }
}
