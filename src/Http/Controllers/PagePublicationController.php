<?php

declare(strict_types=1);

namespace J87NL\VueCms\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use J87NL\Cms\Actions\ChangePageStatus;
use J87NL\Cms\Actions\MakeHomePage;
use J87NL\Cms\Enums\PageStatus;
use J87NL\Cms\Models\Page;

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

    public function home(Page $page, MakeHomePage $makeHomePage): RedirectResponse
    {
        $makeHomePage->handle($page);

        $this->toast(__('vue-cms::pages.flash.home'));

        return to_route('cms.pages.edit', $page);
    }
}
