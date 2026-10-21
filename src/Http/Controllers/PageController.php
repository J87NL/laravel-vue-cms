<?php

declare(strict_types=1);

namespace J87NL\VueCms\Http\Controllers;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use J87NL\Cms\Actions\ChangePageStatus;
use J87NL\Cms\Actions\DeletePage;
use J87NL\Cms\Actions\MakeHomePage;
use J87NL\Cms\Actions\MovePage;
use J87NL\Cms\Actions\RecordRevision;
use J87NL\Cms\Actions\SavePage;
use J87NL\Cms\Blocks\BlockRegistry;
use J87NL\Cms\CmsPermission;
use J87NL\Cms\Http\Controllers\PreviewController;
use J87NL\Cms\Models\Page;
use J87NL\VueCms\Forms\PageForm;
use J87NL\VueCms\Forms\SchedulePageForm;
use J87NL\VueCms\Support\PageWriteAccess;
use J87NL\VueCms\Support\PendingRevisionSummary;
use J87NL\VueCms\Tables\BlockTable;
use J87NL\VueCms\Tables\RevisionTable;

class PageController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        $pages = Page::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return Inertia::render($this->page('pages_index'), [
            'tree' => $this->tree($pages, null, $user),
            'createUrl' => route('cms.pages.create'),
            'breadcrumbs' => [
                [
                    'title' => __('vue-cms::pages.index.title'),
                    'href' => route('cms.pages.index'),
                ],
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render($this->page('form'), [
            'title' => __('vue-cms::pages.create.title'),
            'description' => __('vue-cms::pages.create.description'),
            'breadcrumbs' => [
                [
                    'title' => __('vue-cms::pages.index.title'),
                    'href' => route('cms.pages.index'),
                ],
                [
                    'title' => __('vue-cms::pages.create.title'),
                    'href' => route('cms.pages.create'),
                ],
            ],
            'backUrl' => route('cms.pages.index'),
            'form' => PageForm::make()
                ->action(route('cms.pages.store'))
                ->method('POST'),
        ]);
    }

    public function store(Request $request, SavePage $savePage): RedirectResponse
    {
        $page = $savePage->handle(new Page, PageForm::make()->validate($request), $this->userId($request));

        $this->toast(__('vue-cms::pages.flash.created', [
            'title' => $page->title,
        ]));

        return to_route('cms.pages.edit', $page);
    }

    public function edit(Request $request, Page $page, BlockRegistry $registry, MakeHomePage $makeHomePage, PendingRevisionSummary $pendingRevisionSummary): Response
    {
        $canPublish = (bool) $request->user()?->can(CmsPermission::PagesPublish->value);
        $writeBlockedReason = PageWriteAccess::blockedReason($request->user(), $page);

        return Inertia::render($this->page('pages_edit'), [
            'page' => [
                'id' => $page->id,
                'title' => $page->title,
                'path' => sprintf('/%s', $page->path),
                'url' => url($page->url()),
                'status' => $page->status->value,
                'statusLabel' => $page->status->label(),
                'isPublished' => $page->isPublished(),
                'isHome' => $page->is_home,
            ],
            'breadcrumbs' => [
                [
                    'title' => __('vue-cms::pages.index.title'),
                    'href' => route('cms.pages.index'),
                ],
                [
                    'title' => $page->title,
                    'href' => route('cms.pages.edit', $page),
                ],
            ],
            'form' => PageForm::make($page, ! is_null($writeBlockedReason))
                ->action(route('cms.pages.update', $page))
                ->method('PUT')
                ->fill([
                    ...$page->only(Page::CONTENT_ATTRIBUTES),
                    'noindex' => $page->noindex ? '1' : '0',
                ]),
            'write' => [
                'blockedReason' => $writeBlockedReason,
            ],
            'blocks' => BlockTable::build($page),
            'blockTypes' => array_map(
                fn (string $key): array => [
                    'key' => $key,
                    'label' => __($registry->find($key)->label()),
                    'createUrl' => route('cms.blocks.create', [$page, $key]),
                ],
                array_keys($registry->all()),
            ),
            'revisions' => RevisionTable::build($page),
            'revisionsDescription' => trans_choice('vue-cms::pages.edit.revisions_description', RecordRevision::keep(), [
                'count' => RecordRevision::keep(),
            ]),
            'publication' => [
                'canPublish' => $canPublish,
                'publishUrl' => route('cms.pages.publish', $page),
                'unpublishUrl' => route('cms.pages.unpublish', $page),
                'homeUrl' => route('cms.pages.home', $page),
                'homeBlockedReason' => $makeHomePage->blockedReason($page),
                'unpublishBlockedReason' => ChangePageStatus::unpublishBlockedReason($page),
                'publishAt' => $page->publish_at?->toIso8601String(),
                'unpublishAt' => $page->unpublish_at?->toIso8601String(),
                'scheduleError' => $page->schedule_error,
                'scheduleForm' => $canPublish
                    ? SchedulePageForm::make($page)
                        ->action(route('cms.pages.schedule', $page))
                        ->method('POST')
                        ->fill([
                            'publish_at' => $page->publish_at?->format('Y-m-d H:i:s'),
                            'unpublish_at' => $page->unpublish_at?->format('Y-m-d H:i:s'),
                        ])
                    : null,
            ],
            'pendingRevision' => $pendingRevisionSummary->for($page),
            'previewUrl' => PreviewController::url($page),
            'deletion' => [
                'url' => route('cms.pages.destroy', $page),
                'blockedReason' => $this->deleteBlockedReason($page, $canPublish),
            ],
        ]);
    }

    public function update(Request $request, Page $page, SavePage $savePage): RedirectResponse
    {
        Gate::authorize(PageWriteAccess::ABILITY, $page);

        $data = PageForm::make($page)->validate($request);

        $this->writeLocked($request, $page, fn (Page $locked): Page => $savePage->handle($locked, $data, $this->userId($request)));

        $this->toast(__('vue-cms::pages.flash.saved'));

        return to_route('cms.pages.edit', $page);
    }

    public function destroy(Request $request, Page $page, DeletePage $deletePage): RedirectResponse
    {
        if ($page->isPublished() && ! $request->user()?->can(CmsPermission::PagesPublish->value)) {
            abort(403);
        }

        $deletePage->handle($page);

        $this->toast(__('vue-cms::pages.flash.deleted', [
            'title' => $page->title,
        ]));

        return to_route('cms.pages.index');
    }

    public function move(Request $request, Page $page, MovePage $movePage): RedirectResponse
    {
        Gate::authorize(PageWriteAccess::ABILITY, $page);

        $validated = $request->validate([
            'target' => [
                'required',
                'integer',
                Rule::exists((new Page)->getTable(), 'id'),
            ],
            'position' => [
                'required',
                Rule::in([
                    MovePage::BEFORE,
                    MovePage::AFTER,
                    MovePage::INSIDE,
                ]),
            ],
        ]);

        $target = Page::query()
            ->findOrFail((int) $validated['target']);

        $this->writeLocked($request, $page, function (Page $locked) use ($request, $movePage, $target, $validated): void {
            $movePage->handle($locked, $target, (string) $validated['position'], $this->userId($request));
        });

        $this->toast(__('vue-cms::pages.flash.moved'));

        return to_route('cms.pages.index');
    }

    private function deleteBlockedReason(Page $page, bool $canPublish): ?string
    {
        $reason = DeletePage::blockedReason($page);

        if (! is_null($reason)) {
            return $reason;
        }

        if ($page->isPublished() && ! $canPublish) {
            return __('vue-cms::pages.publication.delete_requires_publish');
        }

        return null;
    }

    /**
     * @param  EloquentCollection<int, Page>  $pages
     * @return list<array<string, mixed>>
     */
    private function tree(EloquentCollection $pages, ?int $parentId, mixed $user): array
    {
        $nodes = [];

        foreach ($pages->where('parent_id', $parentId) as $page) {
            $nodes[] = [
                'id' => $page->id,
                'label' => $page->title,
                'path' => sprintf('/%s', $page->path),
                'status' => $page->status->value,
                'statusLabel' => $page->status->label(),
                'isHome' => $page->is_home,
                'editUrl' => route('cms.pages.edit', $page),
                'moveUrl' => route('cms.pages.move', $page),
                'moveBlockedReason' => PageWriteAccess::blockedReason($user, $page),
                'children' => $this->tree($pages, $page->id, $user),
            ];
        }

        return $nodes;
    }
}
