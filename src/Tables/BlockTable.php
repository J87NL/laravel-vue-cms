<?php

declare(strict_types=1);

namespace J87NL\VueCms\Tables;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use J87NL\Cms\Actions\ReorderBlocks;
use J87NL\Cms\Blocks\BlockRegistry;
use J87NL\Cms\Blocks\ReferencesMedia;
use J87NL\Cms\CmsPermission;
use J87NL\Cms\Models\Block;
use J87NL\Cms\Models\Page;
use J87NL\VueCms\Support\LockedPageWrite;
use J87NL\VueCms\Support\PageWriteAccess;
use TranquilTools\TableBuilder\AbstractTable;
use TranquilTools\TableBuilder\TableBuilder;

final class BlockTable extends AbstractTable
{
    public function __construct(
        private readonly Page $page,
    ) {}

    public function authorize(Request $request): bool
    {
        return (bool) $request->user()?->can(CmsPermission::PagesEdit->value);
    }

    public function authorizeReorder(Request $request): bool
    {
        return (bool) $request->user()?->can(PageWriteAccess::ABILITY, $this->page);
    }

    /**
     * @return array<string, int>
     */
    public function signedParameters(): array
    {
        return [
            'page' => $this->page->id,
        ];
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public static function fromSignedParameters(array $parameters, Request $request): static
    {
        return new self(Page::query()
            ->findOrFail((int) ($parameters['page'] ?? 0)));
    }

    /**
     * @return Builder<Block>
     */
    public function for(): Builder
    {
        return Block::query()
            ->where('page_id', $this->page->id)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function configure(TableBuilder $table): void
    {
        $registry = app(BlockRegistry::class);
        $blockedReason = PageWriteAccess::blockedReason(request()->user(), $this->page);
        $disabled = is_null($blockedReason) ? false : $blockedReason;

        $table
            ->column('type', __('vue-cms::blocks.fields.type'), as: fn (mixed $value, Block $block): string => __($registry->get($block->type)?->label() ?? $block->type))
            ->column('summary', __('vue-cms::blocks.fields.summary'), as: fn (mixed $value, Block $block): string => $this->summary($registry, $block))
            ->column('is_visible', __('vue-cms::blocks.fields.is_visible'), boolean: true)
            ->when(is_null($blockedReason), fn (TableBuilder $table): TableBuilder => $table->reorderable('sort_order'))
            ->rowAction(
                __('vue-cms::blocks.actions.edit'),
                url: fn (Block $block): ?string => $registry->has($block->type) ? route('cms.blocks.edit', [$this->page, $block]) : null,
                disabled: $disabled,
                key: 'edit',
            )
            ->rowAction(
                __('vue-cms::blocks.actions.hide'),
                url: fn (Block $block): string => route('cms.blocks.toggle', [$this->page, $block]),
                method: 'post',
                visible: fn (Block $block): bool => $block->is_visible,
                disabled: $disabled,
                key: 'hide',
            )
            ->rowAction(
                __('vue-cms::blocks.actions.show'),
                url: fn (Block $block): string => route('cms.blocks.toggle', [$this->page, $block]),
                method: 'post',
                visible: fn (Block $block): bool => ! $block->is_visible,
                disabled: $disabled,
                key: 'show',
            )
            ->rowAction(
                __('vue-cms::blocks.actions.delete'),
                url: fn (Block $block): string => route('cms.blocks.destroy', [$this->page, $block]),
                method: 'delete',
                confirm: __('vue-cms::blocks.actions.delete_confirm'),
                variant: 'destructive',
                disabled: $disabled,
                key: 'delete',
            );
    }

    public function performReorder(int|string $id, int|string $toId): void
    {
        app(LockedPageWrite::class)->run(auth()->user(), $this->page, function (Page $locked) use ($id, $toId): void {
            $userId = auth()->id();

            app(ReorderBlocks::class)->move($locked, (int) $id, (int) $toId, is_numeric($userId) ? (int) $userId : null);
        });
    }

    private function summary(BlockRegistry $registry, Block $block): string
    {
        $type = $registry->get($block->type);

        if (is_null($type)) {
            return '';
        }

        $text = $type->toPlainText($block->data);

        if ($text !== '') {
            return Str::limit($text, 80);
        }

        if (! $type instanceof ReferencesMedia) {
            return '';
        }

        return trans_choice('vue-cms::blocks.image_count', count($type->mediaIds($block->data)));
    }
}
