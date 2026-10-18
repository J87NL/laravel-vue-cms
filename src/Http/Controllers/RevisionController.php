<?php

declare(strict_types=1);

namespace J87NL\VueCms\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use J87NL\Cms\Actions\RestoreRevision;
use J87NL\Cms\Models\Page;
use J87NL\Cms\Models\Revision;
use J87NL\VueCms\Support\PageWriteAccess;

class RevisionController extends Controller
{
    public function restore(Request $request, Page $page, Revision $revision, RestoreRevision $restoreRevision): RedirectResponse
    {
        Gate::authorize(PageWriteAccess::ABILITY, $page);

        $this->writeLocked($request, $page, fn (): Page => $restoreRevision->handle($revision, $this->userId($request)));

        $this->toast(__('vue-cms::revisions.flash.restored'));

        return to_route('cms.pages.edit', $page);
    }
}
