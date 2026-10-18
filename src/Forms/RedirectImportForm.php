<?php

declare(strict_types=1);

namespace J87NL\VueCms\Forms;

use TranquilTools\FormBuilder\AbstractForm;
use TranquilTools\FormBuilder\Fields\BaseField;
use TranquilTools\FormBuilder\Fields\File;
use TranquilTools\FormBuilder\Fields\Submit;
use TranquilTools\FormBuilder\FormConfig;

class RedirectImportForm extends AbstractForm
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
            File::make('file')
                ->label(__('vue-cms::redirects.fields.file'))
                ->help(__('vue-cms::redirects.help.file'))
                ->accept([
                    'text/csv',
                    'text/plain',
                    '.csv',
                ])
                ->required()
                ->rules([
                    'file',
                    'mimes:csv,txt',
                    'max:1024',
                ]),

            Submit::make()->label(__('vue-cms::redirects.actions.import')),
        ];
    }
}
