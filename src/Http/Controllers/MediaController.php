<?php

declare(strict_types=1);

namespace J87NL\VueCms\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Inertia\Inertia;
use Inertia\Response;
use J87NL\Cms\Actions\DeleteMedia;
use J87NL\Cms\Actions\UpdateMedia;
use J87NL\Cms\Actions\UploadMedia;
use J87NL\Cms\Models\MediaAsset;
use J87NL\VueCms\Forms\MediaForm;
use J87NL\VueCms\Forms\MediaUploadForm;
use J87NL\VueCms\Tables\MediaTable;

class MediaController extends Controller
{
    public function index(): Response
    {
        return Inertia::render($this->page('media_index'), [
            'media' => MediaTable::build(),
            'createUrl' => route('cms.media.create'),
            'breadcrumbs' => [
                [
                    'title' => __('vue-cms::media.index.title'),
                    'href' => route('cms.media.index'),
                ],
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render($this->page('form'), [
            'title' => __('vue-cms::media.create.title'),
            'description' => __('vue-cms::media.create.description'),
            'breadcrumbs' => $this->breadcrumbs(__('vue-cms::media.create.title'), route('cms.media.create')),
            'backUrl' => route('cms.media.index'),
            'form' => MediaUploadForm::make()
                ->action(route('cms.media.store'))
                ->method('POST'),
        ]);
    }

    public function store(Request $request, UploadMedia $uploadMedia): RedirectResponse
    {
        $validated = MediaUploadForm::make()->validate($request);

        $file = $request->file('file');

        if (! $file instanceof UploadedFile) {
            abort(422);
        }

        $uploadMedia->handle($file, Arr::except($validated, ['file']));

        $this->toast(__('vue-cms::media.flash.uploaded'));

        return to_route('cms.media.index');
    }

    public function edit(MediaAsset $asset): Response
    {
        return Inertia::render($this->page('form'), [
            'title' => __('vue-cms::media.edit.title'),
            'description' => $asset->alt,
            'breadcrumbs' => $this->breadcrumbs($asset->alt, route('cms.media.edit', $asset)),
            'backUrl' => route('cms.media.index'),
            'preview' => [
                'url' => $asset->thumbnailUrl(),
                'alt' => $asset->alt,
                'objectPosition' => $asset->objectPosition(),
            ],
            'form' => MediaForm::make()
                ->action(route('cms.media.update', $asset))
                ->method('PUT')
                ->fill($asset->only([
                    'alt',
                    'caption',
                    'focal_x',
                    'focal_y',
                ])),
        ]);
    }

    public function update(Request $request, MediaAsset $asset, UpdateMedia $updateMedia): RedirectResponse
    {
        $updateMedia->handle($asset, MediaForm::make()->validate($request));

        $this->toast(__('vue-cms::media.flash.saved'));

        return to_route('cms.media.index');
    }

    public function destroy(MediaAsset $asset, DeleteMedia $deleteMedia): RedirectResponse
    {
        $deleteMedia->handle($asset);

        $this->toast(__('vue-cms::media.flash.deleted'));

        return to_route('cms.media.index');
    }

    /**
     * @return list<array{title: string, href: string}>
     */
    private function breadcrumbs(string $title, string $href): array
    {
        return [
            [
                'title' => __('vue-cms::media.index.title'),
                'href' => route('cms.media.index'),
            ],
            [
                'title' => $title,
                'href' => $href,
            ],
        ];
    }
}
