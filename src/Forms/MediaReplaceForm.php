<?php

declare(strict_types=1);

namespace J87NL\VueCms\Forms;

use J87NL\Cms\Models\MediaAsset;
use TranquilTools\FormBuilder\AbstractForm;
use TranquilTools\FormBuilder\Fields\BaseField;
use TranquilTools\FormBuilder\Fields\File;
use TranquilTools\FormBuilder\FormConfig;

class MediaReplaceForm extends AbstractForm
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
                ->label(__('vue-cms::media.fields.file'))
                ->accept(self::accept())
                ->required()
                ->rules([
                    'file',
                    sprintf('mimetypes:%s', implode(',', self::accept())),
                    ...MediaUploadForm::sizeRules(),
                ]),
        ];
    }

    /**
     * @return list<string>
     */
    public static function accept(): array
    {
        return MediaAsset::ACCEPTED_MIME_TYPES;
    }
}
