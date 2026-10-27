<?php

declare(strict_types=1);

namespace J87NL\VueCms\Forms;

use J87NL\Cms\Models\SiteSettings;
use TranquilTools\FormBuilder\AbstractForm;
use TranquilTools\FormBuilder\Fields\BaseField;
use TranquilTools\FormBuilder\Fields\Password;
use TranquilTools\FormBuilder\Fields\Submit;
use TranquilTools\FormBuilder\Fields\Text;
use TranquilTools\FormBuilder\FormConfig;

class SpamProtectionForm extends AbstractForm
{
    public function __construct(
        private readonly bool $hasTurnstileSecret = false,
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
            Text::make('contact_turnstile_site_key')
                ->label(__('vue-cms::spam_protection.fields.contact_turnstile_site_key'))
                ->help(__('vue-cms::spam_protection.help.contact_turnstile_site_key'))
                ->attributes([
                    'autocomplete' => 'off',
                ])
                ->rules([
                    'nullable',
                    'string',
                    'max:255',
                ]),

            Password::make(SiteSettings::TURNSTILE_SECRET)
                ->label(__('vue-cms::spam_protection.fields.contact_turnstile_secret_key'))
                ->help(__('vue-cms::spam_protection.help.contact_turnstile_secret_key'))
                ->stored($this->hasTurnstileSecret)
                ->removable($this->hasTurnstileSecret, SiteSettings::REMOVE_TURNSTILE_SECRET)
                ->attributes([
                    'autocomplete' => 'off',
                ])
                ->rules([
                    'nullable',
                    'string',
                    'max:255',
                ]),

            Submit::make()->label(__('vue-cms::spam_protection.actions.save')),
        ];
    }
}
