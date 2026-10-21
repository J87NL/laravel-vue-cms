<?php

declare(strict_types=1);

namespace J87NL\VueCms\Tables;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use J87NL\Cms\CmsPermission;
use J87NL\Cms\Models\Page;
use J87NL\Cms\Models\Revision;
use J87NL\VueCms\Support\PageWriteAccess;
use TranquilTools\TableBuilder\AbstractTable;
use TranquilTools\TableBuilder\TableBuilder;

final class RevisionTable extends AbstractTable
{
    public function __construct(
        private readonly Page $page,
    ) {}

    public function authorize(Request $request): bool
    {
        return (bool) $request->user()?->can(CmsPermission::PagesEdit->value);
    }

    /**
     * @return Builder<Revision>
     */
    public function for(): Builder
    {
        return Revision::query()
            ->history()
            ->with('author')
            ->where('page_id', $this->page->id)
            ->orderByDesc('id');
    }

    public function configure(TableBuilder $table): void
    {
        $latestId = Revision::query()
            ->history()
            ->where('page_id', $this->page->id)
            ->max('id');

        $blockedReason = PageWriteAccess::blockedReason(request()->user(), $this->page);

        $table
            ->name('revisions')
            ->column('created_at', __('vue-cms::revisions.fields.created_at'), as: fn (mixed $value, Revision $revision): string => (string) $revision->created_at?->format('d-m-Y H:i:s'))
            ->column('author', __('vue-cms::revisions.fields.author'), as: fn (mixed $value, Revision $revision): string => (string) ($revision->author?->getAttribute('name') ?? __('vue-cms::revisions.unknown_author')))
            ->column('title', __('vue-cms::revisions.fields.title'), as: fn (mixed $value, Revision $revision): string => (string) ($revision->snapshot['page']['title'] ?? ''))
            ->column('blocks', __('vue-cms::revisions.fields.blocks'), as: fn (mixed $value, Revision $revision): int => count($revision->snapshot['blocks'] ?? []))
            ->paginate(10);

        $table->rowAction(
            __('vue-cms::revisions.actions.restore'),
            url: fn (Revision $revision): string => route('cms.revisions.restore', [$this->page, $revision]),
            method: 'post',
            confirm: __('vue-cms::revisions.actions.restore_confirm'),
            disabled: fn (Revision $revision): string|false => $this->restoreBlockedReason($revision, $latestId, $blockedReason),
            key: 'restore',
        );
    }

    private function restoreBlockedReason(Revision $revision, mixed $latestId, ?string $blockedReason): string|false
    {
        if (! is_null($blockedReason)) {
            return $blockedReason;
        }

        return $revision->id === $latestId ? (string) __('vue-cms::revisions.current') : false;
    }
}
