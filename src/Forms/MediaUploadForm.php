<?php

declare(strict_types=1);

namespace J87NL\VueCms\Forms;

use J87NL\Cms\Models\MediaAsset;
use TranquilTools\FormBuilder\AbstractForm;
use TranquilTools\FormBuilder\Fields\BaseField;
use TranquilTools\FormBuilder\Fields\File;
use TranquilTools\FormBuilder\Fields\Submit;
use TranquilTools\FormBuilder\FormConfig;

class MediaUploadForm extends AbstractForm
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
                ->accept(MediaAsset::ACCEPTED_MIME_TYPES)
                ->preview()
                ->required()
                ->rules([
                    'file',
                    sprintf('max:%d', (int) config('cms.media.max_upload_kilobytes', 10240)),
                ]),

            ...MediaForm::detailFields(),

            Submit::make()->label(__('vue-cms::media.actions.upload')),
        ];
    }
}
