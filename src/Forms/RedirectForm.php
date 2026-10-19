<?php

declare(strict_types=1);

namespace J87NL\VueCms\Forms;

use J87NL\Cms\Models\Redirect;
use J87NL\Cms\Validation\RedirectRules;
use TranquilTools\FormBuilder\AbstractForm;
use TranquilTools\FormBuilder\Fields\BaseField;
use TranquilTools\FormBuilder\Fields\Select;
use TranquilTools\FormBuilder\Fields\Submit;
use TranquilTools\FormBuilder\Fields\Text;
use TranquilTools\FormBuilder\FormConfig;

class RedirectForm extends AbstractForm
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
            Text::make('from_path')
                ->label(__('vue-cms::redirects.fields.from_path'))
                ->help(__('vue-cms::redirects.help.from_path'))
                ->required()
                ->rules(RedirectRules::for('from_path')),

            Text::make('to')
                ->label(__('vue-cms::redirects.fields.to'))
                ->help(__('vue-cms::redirects.help.to'))
                ->required()
                ->rules(RedirectRules::for('to')),

            Select::make('status_code')
                ->label(__('vue-cms::redirects.fields.status_code'))
                ->options(array_map(
                    fn (int $code): array => [
                        'value' => (string) $code,
                        'label' => __(sprintf('vue-cms::redirects.status_codes.%d', $code)),
                    ],
                    Redirect::STATUS_CODES,
                ))
                ->optionLabel('label')
                ->optionValue('value')
                ->required()
                ->rules(RedirectRules::for('status_code')),

            Submit::make()->label(__('vue-cms::redirects.actions.save')),
        ];
    }
}
