<?php

declare(strict_types=1);

namespace J87NL\VueCms;

use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use J87NL\Cms\CmsPermission;
use J87NL\Cms\Models\Page;
use J87NL\VueCms\Support\PageWriteAccess;

class VueCmsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/vue-cms.php', 'vue-cms');

        $this->registerNavigation();
    }

    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'vue-cms');
        $this->loadRoutesFrom(__DIR__.'/../routes/admin.php');

        Gate::define(PageWriteAccess::ABILITY, fn (Authorizable $user, Page $page): bool => PageWriteAccess::allows($user, $page));

        $this->publishes([
            __DIR__.'/../config/vue-cms.php' => config_path('vue-cms.php'),
        ], 'vue-cms-config');
    }

    private function registerNavigation(): void
    {
        $key = (string) config('vue-cms.navigation_config_key');

        $items = [
            [
                'key' => 'cms.pages',
                'label' => 'vue-cms::navigation.pages',
                'route' => 'cms.pages.index',
                'icon' => 'FileText',
                'permission' => CmsPermission::PagesEdit->value,
                'order' => 100,
            ],
            [
                'key' => 'cms.media',
                'label' => 'vue-cms::navigation.media',
                'route' => 'cms.media.index',
                'icon' => 'Image',
                'permission' => CmsPermission::Media->value,
                'order' => 110,
            ],
            [
                'key' => 'cms.menus',
                'label' => 'vue-cms::navigation.menus',
                'route' => 'cms.menus.index',
                'icon' => 'Menu',
                'permission' => CmsPermission::Menus->value,
                'order' => 120,
            ],
            [
                'key' => 'cms.redirects',
                'label' => 'vue-cms::navigation.redirects',
                'route' => 'cms.redirects.index',
                'icon' => 'Signpost',
                'permission' => CmsPermission::Redirects->value,
                'order' => 130,
            ],
            [
                'key' => 'cms.settings',
                'label' => 'vue-cms::navigation.settings',
                'route' => 'cms.settings.edit',
                'icon' => 'Settings2',
                'permission' => CmsPermission::Settings->value,
                'order' => 140,
            ],
        ];

        $registered = config($key, []);
        $registeredKeys = is_array($registered) ? array_column($registered, 'key') : [];

        foreach ($items as $item) {
            if (in_array($item['key'], $registeredKeys, true)) {
                continue;
            }

            config()->push($key, $item);
        }
    }
}
