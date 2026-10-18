<?php

declare(strict_types=1);

namespace J87NL\VueCms\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use J87NL\Cms\Actions\SaveSiteSettings;
use J87NL\Cms\Models\SiteSettings;
use J87NL\VueCms\Forms\SettingsForm;

class SettingsController extends Controller
{
    public function edit(): Response
    {
        $settings = SiteSettings::current();

        return Inertia::render($this->page('form'), [
            'title' => __('vue-cms::settings.edit.title'),
            'description' => __('vue-cms::settings.edit.description'),
            'breadcrumbs' => [
                [
                    'title' => __('vue-cms::settings.edit.title'),
                    'href' => route('cms.settings.edit'),
                ],
            ],
            'backUrl' => route('cms.pages.index'),
            'form' => SettingsForm::make()
                ->action(route('cms.settings.update'))
                ->method('PUT')
                ->fill([
                    ...$settings->only(SiteSettings::EDITABLE_ATTRIBUTES),
                    'site_name' => $settings->siteName(),
                    'entity_type' => $settings->entity_type->value,
                    'entity_same_as' => implode("\n", $settings->sameAs()),
                ]),
        ]);
    }

    public function update(Request $request, SaveSiteSettings $saveSiteSettings): RedirectResponse
    {
        $saveSiteSettings->handle(SettingsForm::make()->validate($request));

        $this->toast(__('vue-cms::settings.flash.saved'));

        return to_route('cms.settings.edit');
    }
}
