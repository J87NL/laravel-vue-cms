<?php

declare(strict_types=1);

namespace J87NL\VueCms\Tables;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use J87NL\Cms\CmsPermission;
use J87NL\Cms\Models\Redirect;
use TranquilTools\TableBuilder\AbstractTable;
use TranquilTools\TableBuilder\TableBuilder;

final class RedirectTable extends AbstractTable
{
    public function authorize(Request $request): bool
    {
        return (bool) $request->user()?->can(CmsPermission::Redirects->value);
    }

    /**
     * @return Builder<Redirect>
     */
    public function for(): Builder
    {
        return Redirect::query()
            ->orderBy('from_path');
    }

    public function configure(TableBuilder $table): void
    {
        $table
            ->withGlobalSearch(columns: [
                'from_path',
                'to',
            ])
            ->column('from_path', __('vue-cms::redirects.fields.from_path'), as: fn (mixed $value, Redirect $redirect): string => $redirect->fromUrl())
            ->column('to', __('vue-cms::redirects.fields.to'))
            ->column('status_code', __('vue-cms::redirects.fields.status_code'))
            ->column('source', __('vue-cms::redirects.fields.source'), as: fn (mixed $value, Redirect $redirect): string => $redirect->source->label())
            ->column('hits', __('vue-cms::redirects.fields.hits'))
            ->column('last_hit_at', __('vue-cms::redirects.fields.last_hit_at'), as: fn (mixed $value, Redirect $redirect): string => $redirect->last_hit_at?->isoFormat('D MMM YYYY HH:mm') ?? '')
            ->paginate(50);

        $table
            ->rowAction(
                __('vue-cms::redirects.actions.edit'),
                url: fn (Redirect $redirect): string => route('cms.redirects.edit', $redirect),
                key: 'edit',
            )
            ->rowAction(
                __('vue-cms::redirects.actions.delete'),
                url: fn (Redirect $redirect): string => route('cms.redirects.destroy', $redirect),
                method: 'delete',
                confirm: __('vue-cms::redirects.actions.delete_confirm'),
                variant: 'destructive',
                key: 'delete',
            );
    }
}
