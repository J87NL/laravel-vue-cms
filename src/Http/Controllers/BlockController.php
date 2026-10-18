<?php

declare(strict_types=1);

namespace J87NL\VueCms\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
use J87NL\Cms\Actions\DeleteBlock;
use J87NL\Cms\Actions\SaveBlock;
use J87NL\Cms\Actions\ToggleBlockVisibility;
use J87NL\Cms\Blocks\BlockRegistry;
use J87NL\Cms\Blocks\BlockType;
use J87NL\Cms\Models\Block;
use J87NL\Cms\Models\Page;
use J87NL\VueCms\Forms\BlockForm;
use J87NL\VueCms\Support\PageWriteAccess;
use TranquilTools\FormBuilder\FormConfig;

class BlockController extends Controller
{
    public function __construct(
        private readonly BlockRegistry $registry,
    ) {}

    public function create(Page $page, string $type): Response
    {
        Gate::authorize(PageWriteAccess::ABILITY, $page);

        $blockType = $this->type($type);

        return $this->form($page, $blockType, __('vue-cms::blocks.create.title', [
            'type' => __($blockType->label()),
        ]), BlockForm::make($blockType)
            ->action(route('cms.blocks.store', [$page, $blockType->key()]))
            ->method('POST'));
    }

    public function store(Request $request, Page $page, string $type, SaveBlock $saveBlock): RedirectResponse
    {
        Gate::authorize(PageWriteAccess::ABILITY, $page);

        $blockType = $this->type($type);

        $data = $this->validated($request, $blockType);

        $this->writeLocked($request, $page, fn (Page $locked): Block => $saveBlock->handle($locked, new Block, $blockType, $data, $this->userId($request)));

        $this->toast(__('vue-cms::blocks.flash.created'));

        return to_route('cms.pages.edit', $page);
    }

    public function edit(Page $page, Block $block): Response
    {
        Gate::authorize(PageWriteAccess::ABILITY, $page);

        $blockType = $this->type($block->type);

        return $this->form($page, $blockType, __('vue-cms::blocks.edit.title', [
            'type' => __($blockType->label()),
        ]), BlockForm::make($blockType)
            ->action(route('cms.blocks.update', [$page, $block]))
            ->method('PUT')
            ->fill($block->data));
    }

    public function update(Request $request, Page $page, Block $block, SaveBlock $saveBlock): RedirectResponse
    {
        Gate::authorize(PageWriteAccess::ABILITY, $page);

        $blockType = $this->type($block->type);

        $data = $this->validated($request, $blockType);

        $this->writeLocked($request, $page, fn (Page $locked): Block => $saveBlock->handle($locked, $block, $blockType, $data, $this->userId($request)));

        $this->toast(__('vue-cms::blocks.flash.saved'));

        return to_route('cms.pages.edit', $page);
    }

    public function destroy(Request $request, Page $page, Block $block, DeleteBlock $deleteBlock): RedirectResponse
    {
        Gate::authorize(PageWriteAccess::ABILITY, $page);

        $this->writeLocked($request, $page, function () use ($request, $block, $deleteBlock): void {
            $deleteBlock->handle($block, $this->userId($request));
        });

        $this->toast(__('vue-cms::blocks.flash.deleted'));

        return to_route('cms.pages.edit', $page);
    }

    public function toggle(Request $request, Page $page, Block $block, ToggleBlockVisibility $toggleBlockVisibility): RedirectResponse
    {
        Gate::authorize(PageWriteAccess::ABILITY, $page);

        $this->writeLocked($request, $page, function () use ($request, $block, $toggleBlockVisibility): void {
            $toggleBlockVisibility->handle($block, $this->userId($request));
        });

        $this->toast(__('vue-cms::blocks.flash.saved'));

        return to_route('cms.pages.edit', $page);
    }

    private function type(string $key): BlockType
    {
        $type = $this->registry->get($key);

        if (is_null($type)) {
            abort(404);
        }

        return $type;
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, BlockType $type): array
    {
        return $request->validate($type->rules());
    }

    private function form(Page $page, BlockType $type, string $title, FormConfig $form): Response
    {
        return Inertia::render($this->page('form'), [
            'title' => $title,
            'description' => $page->title,
            'breadcrumbs' => [
                [
                    'title' => __('vue-cms::pages.index.title'),
                    'href' => route('cms.pages.index'),
                ],
                [
                    'title' => $page->title,
                    'href' => route('cms.pages.edit', $page),
                ],
                [
                    'title' => __($type->label()),
                    'href' => route('cms.pages.edit', $page),
                ],
            ],
            'backUrl' => route('cms.pages.edit', $page),
            'form' => $form,
        ]);
    }
}
