<?php

declare(strict_types=1);

namespace J87NL\VueCms\Forms;

use J87NL\Cms\Models\Page;
use TranquilTools\FormBuilder\AbstractForm;
use TranquilTools\FormBuilder\Fields\BaseField;
use TranquilTools\FormBuilder\Fields\Date;
use TranquilTools\FormBuilder\Fields\DateTime;
use TranquilTools\FormBuilder\Fields\Section;
use TranquilTools\FormBuilder\Fields\Submit;
use TranquilTools\FormBuilder\FormConfig;

class SchedulePageForm extends AbstractForm
{
    public function __construct(
        private readonly Page $page,
    ) {}

    public function configure(FormConfig $form): void
    {
        $form->class('space-y-4');
    }

    /**
     * @return list<BaseField>
     */
    public function fields(): array
    {
        $fields = [];

        if (! $this->page->isPublished()) {
            $fields[] = DateTime::make('publish_at')
                ->label(__('vue-cms::pages.fields.publish_at'))
                ->help(__('vue-cms::pages.help.publish_at'))
                ->timezone(Date::BROWSER_TIMEZONE)
                ->rules([
                    'nullable',
                    'date',
                ]);
        }

        $fields[] = DateTime::make('unpublish_at')
            ->label(__('vue-cms::pages.fields.unpublish_at'))
            ->help(__('vue-cms::pages.help.unpublish_at'))
            ->timezone(Date::BROWSER_TIMEZONE)
            ->rules([
                'nullable',
                'date',
            ]);

        return [
            Section::make()
                ->columns(2)
                ->fields($fields),

            Submit::make()->label(__('vue-cms::pages.actions.save_schedule')),
        ];
    }
}
