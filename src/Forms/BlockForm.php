<?php

declare(strict_types=1);

namespace J87NL\VueCms\Forms;

use J87NL\Cms\Blocks\BlockType;
use TranquilTools\FormBuilder\AbstractForm;
use TranquilTools\FormBuilder\Fields\BaseField;
use TranquilTools\FormBuilder\Fields\Submit;
use TranquilTools\FormBuilder\FormConfig;

class BlockForm extends AbstractForm
{
    public function __construct(
        private readonly BlockType $type,
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
            ...$this->type->fields(),

            Submit::make()->label(__('vue-cms::blocks.actions.save')),
        ];
    }
}
