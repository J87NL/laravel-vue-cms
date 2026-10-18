<?php

declare(strict_types=1);

namespace J87NL\VueCms\Tables;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\HtmlString;
use J87NL\Cms\CmsPermission;
use J87NL\Cms\Media\MediaUsage;
use J87NL\Cms\Models\MediaAsset;
use TranquilTools\TableBuilder\AbstractTable;
use TranquilTools\TableBuilder\TableBuilder;

final class MediaTable extends AbstractTable
{
    public function authorize(Request $request): bool
    {
        return (bool) $request->user()?->can(CmsPermission::Media->value);
    }

    /**
     * @return Builder<MediaAsset>
     */
    public function for(): Builder
    {
        return MediaAsset::query()
            ->with('media')
            ->orderByDesc('id');
    }

    public function configure(TableBuilder $table): void
    {
        $usage = app(MediaUsage::class)->counts();

        $table
            ->withGlobalSearch(columns: [
                'alt',
                'caption',
            ])
            ->column('preview', __('vue-cms::media.fields.preview'), as: fn (mixed $value, MediaAsset $asset): HtmlString => $this->preview($asset))
            ->column('alt', __('vue-cms::media.fields.alt'))
            ->column('caption', __('vue-cms::media.fields.caption'))
            ->column('usage', __('vue-cms::media.fields.usage'), as: fn (mixed $value, MediaAsset $asset): int => $usage[$asset->id] ?? 0)
            ->paginate(25);

        $table
            ->rowAction(
                __('vue-cms::media.actions.edit'),
                url: fn (MediaAsset $asset): string => route('cms.media.edit', $asset),
                key: 'edit',
            )
            ->rowAction(
                __('vue-cms::media.actions.delete'),
                url: fn (MediaAsset $asset): string => route('cms.media.destroy', $asset),
                method: 'delete',
                confirm: __('vue-cms::media.actions.delete_confirm'),
                variant: 'destructive',
                disabled: fn (MediaAsset $asset): string|false => ($usage[$asset->id] ?? 0) > 0 ? (string) __('cms::validation.media_in_use') : false,
                key: 'delete',
            );
    }

    private function preview(MediaAsset $asset): HtmlString
    {
        $url = $asset->thumbnailUrl();

        if (is_null($url)) {
            return new HtmlString('');
        }

        return new HtmlString(sprintf(
            '<img src="%s" alt="%s" class="h-12 w-16 rounded object-cover" style="object-position: %s" loading="lazy">',
            e($url),
            e($asset->alt),
            e($asset->objectPosition()),
        ));
    }
}
