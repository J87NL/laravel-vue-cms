<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use J87NL\Cms\CmsPermission;
use J87NL\VueCms\Http\Controllers\DashboardController;

Route::middleware(config('vue-cms.admin.middleware'))
    ->prefix(config('vue-cms.admin.prefix'))
    ->name('cms.')
    ->group(function (): void {
        Route::get('/', DashboardController::class)
            ->middleware(sprintf('can:%s', CmsPermission::PagesEdit->value))
            ->name('dashboard');
    });
