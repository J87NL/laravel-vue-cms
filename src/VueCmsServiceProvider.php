<?php

declare(strict_types=1);

namespace J87NL\VueCms;

use Illuminate\Support\ServiceProvider;
use J87NL\Cms\CmsPermission;

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

        $this->publishes([
            __DIR__.'/../config/vue-cms.php' => config_path('vue-cms.php'),
        ], 'vue-cms-config');
    }

    private function registerNavigation(): void
    {
        $key = (string) config('vue-cms.navigation_config_key');

        $alreadyRegistered = collect(config($key, []))->contains('key', 'cms.dashboard');

        if ($alreadyRegistered) {
            return;
        }

        config()->push($key, [
            'key' => 'cms.dashboard',
            'label' => 'vue-cms::navigation.dashboard',
            'route' => 'cms.dashboard',
            'icon' => 'FileText',
            'permission' => CmsPermission::PagesEdit->value,
            'order' => 100,
        ]);
    }
}
