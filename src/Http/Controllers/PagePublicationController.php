<?php

declare(strict_types=1);

namespace J87NL\VueCms\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use J87NL\Cms\Actions\ChangePageStatus;
use J87NL\Cms\Actions\DiscardPendingRevision;
use J87NL\Cms\Actions\MakeHomePage;
use J87NL\Cms\Actions\PublishPage;
use J87NL\Cms\Actions\SchedulePage;
use J87NL\Cms\Enums\PageStatus;
use J87NL\Cms\Models\Page;
use J87NL\VueCms\Forms\SchedulePageForm;
use J87NL\VueCms\Support\PageWriteAccess;

class PagePublicationController extends Controller
{
    public function publish(Request $request, Page $page, ChangePageStatus $changePageStatus): RedirectResponse
    {
        $changePageStatus->handle($page, PageStatus::Published, $this->userId($request));

        $this->toast(__('vue-cms::pages.flash.published'));

        return to_route('cms.pages.edit', $page);
    }

    public function unpublish(Request $request, Page $page, ChangePageStatus $changePageStatus): RedirectResponse
    {
        $changePageStatus->handle($page, PageStatus::Unpublished, $this->userId($request));

        $this->toast(__('vue-cms::pages.flash.unpublished'));

        return to_route('cms.pages.edit', $page);
    }

    public function publishPending(Request $request, Page $page, PublishPage $publishPage): RedirectResponse
    {
        Gate::authorize(PageWriteAccess::ABILITY, $page);

        $publishPage->handle($page, $this->userId($request));

        $this->toast(__('vue-cms::pages.flash.pending_published'));

        return to_route('cms.pages.edit', $page);
    }

    public function discardPending(Page $page, DiscardPendingRevision $discardPendingRevision): RedirectResponse
    {
        Gate::authorize(PageWriteAccess::ABILITY, $page);

        $discardPendingRevision->handle($page);

        $this->toast(__('vue-cms::pages.flash.pending_discarded'));

        return to_route('cms.pages.edit', $page);
    }

    public function schedule(Request $request, Page $page, SchedulePage $schedulePage): RedirectResponse
    {
        $validated = SchedulePageForm::make($page)->validate($request);

        $schedulePage->handle($page, $this->moment($validated['publish_at'] ?? null), $this->moment($validated['unpublish_at'] ?? null));

        $this->toast(__('vue-cms::pages.flash.scheduled'));

        return to_route('cms.pages.edit', $page);
    }

    public function home(Page $page, MakeHomePage $makeHomePage): RedirectResponse
    {
        $makeHomePage->handle($page);

        $this->toast(__('vue-cms::pages.flash.home'));

        return to_route('cms.pages.edit', $page);
    }

    private function moment(mixed $value): ?Carbon
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        return Carbon::parse($value);
    }
}
