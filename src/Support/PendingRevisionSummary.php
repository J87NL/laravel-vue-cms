<?php

declare(strict_types=1);

namespace J87NL\VueCms\Support;

use Illuminate\Database\Eloquent\Model;
use J87NL\Cms\Actions\DiffPendingRevision;
use J87NL\Cms\Blocks\BlockRegistry;
use J87NL\Cms\Http\Controllers\PreviewController;
use J87NL\Cms\Models\MediaAsset;
use J87NL\Cms\Models\Page;
use J87NL\Cms\Models\Revision;
use J87NL\Cms\Pages\PendingRevisionEditor;
use J87NL\Cms\Pages\WordDiff;
use TranquilTools\FormBuilder\Fields\BaseField;

class PendingRevisionSummary
{
    /**
     * @var array<string, list<array<string, mixed>>>
     */
    private array $schemas = [];

    public function __construct(
        private readonly PendingRevisionEditor $pending,
        private readonly DiffPendingRevision $diffPendingRevision,
        private readonly BlockRegistry $registry,
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

        $diff = $this->diffPendingRevision->handle($page, $revision);

        if (is_null($diff)) {
            return null;
        }

        $revision->loadMissing('author');

        $content = PendingRevisionEditor::content($revision->snapshot);

        return [
            'author' => $this->name($revision->author?->getAttribute('name')),
            'editor' => $this->name($this->editorName($revision)),
            'updatedAt' => $revision->updated_at?->toIso8601String(),
            'title' => (string) ($content['page']['title'] ?? ''),
            'fields' => array_map(fn (array $field): string => $this->fieldLabel($field['field']), $diff['fields']),
            'blocks' => [
                'added' => $diff['summary']['added'],
                'removed' => $diff['summary']['removed'],
                'changed' => $diff['summary']['changed'],
                'reordered' => $diff['summary']['reordered'],
            ],
            'diff' => [
                'fields' => array_map(fn (array $field): array => $this->field($field), $diff['fields']),
                'blocks' => array_map(fn (array $block): array => $this->block($block), $diff['blocks']),
            ],
            'previewUrl' => PreviewController::pendingUrl($page, $revision),
            'publishUrl' => route('cms.pages.pending.publish', $page),
            'discardUrl' => route('cms.pages.pending.discard', $page),
        ];
    }

    /**
     * @param  array{field: string, before: mixed, after: mixed}  $field
     * @return array<string, mixed>
     */
    private function field(array $field): array
    {
        $before = $this->fieldValue($field['field'], $field['before']);
        $after = $this->fieldValue($field['field'], $field['after']);

        return [
            'key' => $field['field'],
            'label' => $this->fieldLabel($field['field']),
            'before' => $before,
            'after' => $after,
            'words' => $this->textField($field['field']) && ! is_null($before) && ! is_null($after) ? WordDiff::words($before, $after) : null,
            'markupChanged' => false,
            'links' => null,
        ];
    }

    /**
     * @param  array{id: int, type: string, label: string, status: string, moved: bool, position: array{before: int|null, after: int|null}, visible: array{before: bool|null, after: bool|null}, changes: list<array{key: string, before: mixed, after: mixed, words: list<array{type: string, text: string}>|null, markup_changed: bool, links: array{before: list<string>, after: list<string>}|null}>}  $block
     * @return array<string, mixed>
     */
    private function block(array $block): array
    {
        return [
            'id' => $block['id'],
            'label' => $block['label'],
            'status' => $block['status'],
            'moved' => $block['moved'],
            'position' => $block['position'],
            'visible' => $block['visible'],
            'changes' => array_map(fn (array $change): array => [
                'key' => $change['key'],
                'label' => $this->valueLabel($block['type'], $change['key']),
                'before' => $this->value($change['before']),
                'after' => $this->value($change['after']),
                'words' => $change['words'],
                'markupChanged' => $change['markup_changed'],
                'links' => $change['links'],
            ], $block['changes']),
        ];
    }

    private function fieldLabel(string $field): string
    {
        return (string) __(sprintf('vue-cms::pages.fields.%s', $field));
    }

    private function textField(string $field): bool
    {
        return in_array($field, [
            'title',
            'menu_title',
            'meta_title',
            'meta_description',
        ], true);
    }

    private function fieldValue(string $field, mixed $value): ?string
    {
        if (is_null($value) || $value === '') {
            return null;
        }

        if ($field === 'parent_id') {
            $parent = Page::query()
                ->find((int) $value);

            return is_null($parent) ? sprintf('#%d', (int) $value) : sprintf('%s (/%s)', $parent->title, $parent->path);
        }

        if ($field === 'og_image_id') {
            $asset = MediaAsset::query()
                ->find((int) $value);

            return is_null($asset) || in_array($asset->alt, [
                null,
                '',
            ], true) ? sprintf('#%d', (int) $value) : sprintf('%s (#%d)', $asset->alt, $asset->id);
        }

        return $this->value($value);
    }

    private function value(mixed $value): ?string
    {
        if (is_null($value) || $value === '') {
            return null;
        }

        if (is_bool($value)) {
            return (string) __($value ? 'vue-cms::revisions.diff.yes' : 'vue-cms::revisions.diff.no');
        }

        return is_scalar($value) ? (string) $value : (string) json_encode($value);
    }

    private function valueLabel(string $type, string $key): string
    {
        $fields = $this->schema($type);
        $labels = [];

        foreach (explode('.', $key) as $part) {
            if (ctype_digit($part)) {
                $labels[] = (string) ((int) $part + 1);

                continue;
            }

            $field = collect($fields)->firstWhere('name', $part);
            $label = is_array($field) ? ($field['label'] ?? null) : null;
            $labels[] = is_string($label) && $label !== '' ? $label : $part;
            $fields = is_array($field) && is_array($field['fields'] ?? null) ? $field['fields'] : [];
        }

        return implode(' › ', $labels);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function schema(string $type): array
    {
        if (array_key_exists($type, $this->schemas)) {
            return $this->schemas[$type];
        }

        $blockType = $this->registry->get($type);

        return $this->schemas[$type] = is_null($blockType) ? [] : array_values(array_map(
            fn (BaseField $field): array => $field->toSchema(),
            $blockType->fields(),
        ));
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
}
