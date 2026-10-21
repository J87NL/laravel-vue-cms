<?php

declare(strict_types=1);

namespace J87NL\VueCms\Support;

use Illuminate\Database\Eloquent\Model;
use J87NL\Cms\Actions\RecordRevision;
use J87NL\Cms\Models\Page;
use J87NL\Cms\Models\Revision;
use J87NL\Cms\Pages\PendingRevisionEditor;

class PendingRevisionSummary
{
    public function __construct(
        private readonly PendingRevisionEditor $pending,
        private readonly RecordRevision $recordRevision,
    ) {}

    /**
     * @return array<string, mixed>|null
     */
    public function for(Page $page): ?array
    {
        $revision = $page->isPublished() ? $this->pending->find($page) : null;

        if (is_null($revision)) {
            return null;
        }

        $revision->loadMissing('author');

        $content = PendingRevisionEditor::content($revision->snapshot);
        $base = PendingRevisionEditor::base($revision->snapshot) ?? $this->recordRevision->snapshot($page);
        $pendingBlocks = $this->blocksById($content['blocks']);
        $liveBlocks = $this->blocksById($base['blocks']);
        $common = array_values(array_intersect(array_keys($pendingBlocks), array_keys($liveBlocks)));

        return [
            'author' => $this->name($revision->author?->getAttribute('name')),
            'editor' => $this->name($this->editorName($revision)),
            'updatedAt' => $revision->updated_at?->toIso8601String(),
            'title' => (string) ($content['page']['title'] ?? ''),
            'fields' => $this->changedFields($content['page'], $base['page']),
            'blocks' => [
                'added' => count(array_diff_key($pendingBlocks, $liveBlocks)),
                'removed' => count(array_diff_key($liveBlocks, $pendingBlocks)),
                'changed' => count(array_filter($common, fn (int $id): bool => $this->blockChanged($pendingBlocks[$id], $liveBlocks[$id]))),
                'reordered' => $this->order($pendingBlocks, $common) !== $this->order($liveBlocks, $common),
            ],
            'publishUrl' => route('cms.pages.pending.publish', $page),
            'discardUrl' => route('cms.pages.pending.discard', $page),
        ];
    }

    private function name(mixed $name): string
    {
        return is_string($name) && $name !== '' ? $name : (string) __('vue-cms::revisions.unknown_author');
    }

    private function editorName(Revision $revision): mixed
    {
        $pending = $revision->snapshot['pending'] ?? [];

        if (! is_array($pending) || ! array_key_exists('edited_by', $pending)) {
            return $revision->author?->getAttribute('name');
        }

        $editorId = PendingRevisionEditor::editedBy($revision->snapshot);

        if (is_null($editorId)) {
            return null;
        }

        $model = Revision::userModel();

        if ($model === Model::class) {
            return null;
        }

        return $model::query()
            ->find($editorId)
            ?->getAttribute('name');
    }

    /**
     * @param  array<string, mixed>  $pending
     * @param  array<string, mixed>  $base
     * @return list<string>
     */
    private function changedFields(array $pending, array $base): array
    {
        $labels = [];

        foreach (Page::CONTENT_ATTRIBUTES as $field) {
            if (! array_key_exists($field, $pending)) {
                continue;
            }

            if ($this->normalize($pending[$field]) === $this->normalize($base[$field] ?? null)) {
                continue;
            }

            $labels[] = (string) __(sprintf('vue-cms::pages.fields.%s', $field));
        }

        return $labels;
    }

    private function normalize(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        return (string) $value;
    }

    /**
     * @param  list<array<string, mixed>>  $blocks
     * @return array<int, array<string, mixed>>
     */
    private function blocksById(array $blocks): array
    {
        usort($blocks, fn (array $a, array $b): int => (int) ($a['sort_order'] ?? 0) <=> (int) ($b['sort_order'] ?? 0));

        $byId = [];

        foreach ($blocks as $block) {
            $byId[(int) ($block['id'] ?? 0)] = $block;
        }

        return $byId;
    }

    /**
     * @param  array<string, mixed>  $pending
     * @param  array<string, mixed>  $live
     */
    private function blockChanged(array $pending, array $live): bool
    {
        return ($pending['type'] ?? null) !== ($live['type'] ?? null)
            || ($pending['data'] ?? null) != ($live['data'] ?? null)
            || (bool) ($pending['is_visible'] ?? true) !== (bool) ($live['is_visible'] ?? true);
    }

    /**
     * @param  array<int, array<string, mixed>>  $blocks
     * @param  list<int>  $common
     * @return list<int>
     */
    private function order(array $blocks, array $common): array
    {
        return array_values(array_filter(array_keys($blocks), fn (int $id): bool => in_array($id, $common, true)));
    }
}
