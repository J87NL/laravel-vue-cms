<?php

declare(strict_types=1);

namespace J87NL\VueCms\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
use J87NL\Cms\Actions\SaveSiteSettings;
use J87NL\Cms\CmsPermission;
use J87NL\Cms\Contact\Turnstile;
use J87NL\Cms\Models\SiteSettings;
use J87NL\VueCms\Forms\SpamProtectionForm;

class SpamProtectionController extends Controller
{
    public function edit(Turnstile $turnstile): Response
    {
        $settings = SiteSettings::current();

        return Inertia::render($this->page('form'), [
            'title' => __('vue-cms::spam_protection.edit.title'),
            'description' => __('vue-cms::spam_protection.edit.description'),
            'notice' => $turnstile->enabled()
                ? __('vue-cms::spam_protection.status.active')
                : __('vue-cms::spam_protection.status.inactive'),
            'breadcrumbs' => [
                [
                    'title' => __('vue-cms::spam_protection.edit.title'),
                    'href' => route('cms.spam-protection.edit'),
                ],
            ],
            'backUrl' => Gate::allows(CmsPermission::PagesEdit->value) ? route('cms.pages.index') : null,
            'form' => SpamProtectionForm::make($settings->hasTurnstileSecret())
                ->action(route('cms.spam-protection.update'))
                ->method('PUT')
                ->fill([
                    'contact_turnstile_site_key' => $settings->contact_turnstile_site_key,
                ]),
        ]);
    }

    public function update(Request $request, SaveSiteSettings $saveSiteSettings): RedirectResponse
    {
        $validated = SpamProtectionForm::make(SiteSettings::current()->hasTurnstileSecret())->validate($request);

        $saveSiteSettings->handle([
            'contact_turnstile_site_key' => trim((string) ($validated['contact_turnstile_site_key'] ?? '')) ?: null,
            SiteSettings::TURNSTILE_SECRET => $validated[SiteSettings::TURNSTILE_SECRET] ?? null,
            SiteSettings::REMOVE_TURNSTILE_SECRET => $validated[SiteSettings::REMOVE_TURNSTILE_SECRET] ?? false,
        ]);

        $this->toast(__('vue-cms::spam_protection.flash.saved'));

        return to_route('cms.spam-protection.edit');
    }
}
