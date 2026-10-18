<?php

declare(strict_types=1);

namespace J87NL\VueCms\Support;

use Illuminate\Contracts\Auth\Access\Authorizable;
use J87NL\Cms\CmsPermission;
use J87NL\Cms\Models\Page;

class PageWriteAccess
{
    public const string ABILITY = 'cms.pages.write';

    public static function allows(mixed $user, Page $page): bool
    {
        if (! $user instanceof Authorizable || ! $user->can(CmsPermission::PagesEdit->value)) {
            return false;
        }

        return is_null(self::blockedReason($user, $page));
    }

    public static function blockedReason(mixed $user, Page $page): ?string
    {
        if (! $page->isPublished()) {
            return null;
        }

        if ($user instanceof Authorizable && $user->can(CmsPermission::PagesPublish->value)) {
            return null;
        }

        return __('vue-cms::pages.publication.write_requires_publish');
    }
}
