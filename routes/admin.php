<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use J87NL\Cms\CmsPermission;
use J87NL\VueCms\Http\Controllers\BlockController;
use J87NL\VueCms\Http\Controllers\MediaController;
use J87NL\VueCms\Http\Controllers\MenuController;
use J87NL\VueCms\Http\Controllers\MenuItemController;
use J87NL\VueCms\Http\Controllers\PageController;
use J87NL\VueCms\Http\Controllers\PagePublicationController;
use J87NL\VueCms\Http\Controllers\RedirectController;
use J87NL\VueCms\Http\Controllers\RevisionController;
use J87NL\VueCms\Http\Controllers\SettingsController;

Route::middleware(config('vue-cms.admin.middleware'))
    ->prefix(config('vue-cms.admin.prefix'))
    ->name('cms.')
    ->group(function (): void {
        Route::middleware(sprintf('can:%s', CmsPermission::PagesEdit->value))->group(function (): void {
            Route::redirect('/', sprintf('/%s/pages', trim((string) config('vue-cms.admin.prefix'), '/')))->name('dashboard');

            Route::get('pages', [PageController::class, 'index'])->name('pages.index');
            Route::get('pages/create', [PageController::class, 'create'])->name('pages.create');
            Route::post('pages', [PageController::class, 'store'])->name('pages.store');
            Route::get('pages/{page}/edit', [PageController::class, 'edit'])->name('pages.edit');
            Route::put('pages/{page}', [PageController::class, 'update'])->name('pages.update');
            Route::delete('pages/{page}', [PageController::class, 'destroy'])->name('pages.destroy');
            Route::post('pages/{page}/move', [PageController::class, 'move'])->name('pages.move');

            Route::get('pages/{page}/blocks/create/{type}', [BlockController::class, 'create'])->name('blocks.create');
            Route::post('pages/{page}/blocks/{type}', [BlockController::class, 'store'])->name('blocks.store');
            Route::get('pages/{page}/blocks/{block}/edit', [BlockController::class, 'edit'])->name('blocks.edit')->scopeBindings();
            Route::put('pages/{page}/blocks/{block}', [BlockController::class, 'update'])->name('blocks.update')->scopeBindings();
            Route::delete('pages/{page}/blocks/{block}', [BlockController::class, 'destroy'])->name('blocks.destroy')->scopeBindings();
            Route::post('pages/{page}/blocks/{block}/visibility', [BlockController::class, 'toggle'])->name('blocks.toggle')->scopeBindings();

            Route::post('pages/{page}/revisions/{revision}/restore', [RevisionController::class, 'restore'])->name('revisions.restore')->scopeBindings();

            Route::middleware(sprintf('can:%s', CmsPermission::PagesPublish->value))->group(function (): void {
                Route::post('pages/{page}/publish', [PagePublicationController::class, 'publish'])->name('pages.publish');
                Route::post('pages/{page}/unpublish', [PagePublicationController::class, 'unpublish'])->name('pages.unpublish');
                Route::post('pages/{page}/home', [PagePublicationController::class, 'home'])->name('pages.home');
            });
        });

        Route::middleware(sprintf('can:%s', CmsPermission::Media->value))->group(function (): void {
            Route::get('media', [MediaController::class, 'index'])->name('media.index');
            Route::get('media/create', [MediaController::class, 'create'])->name('media.create');
            Route::post('media', [MediaController::class, 'store'])->name('media.store');
            Route::get('media/{asset}/edit', [MediaController::class, 'edit'])->name('media.edit');
            Route::put('media/{asset}', [MediaController::class, 'update'])->name('media.update');
            Route::delete('media/{asset}', [MediaController::class, 'destroy'])->name('media.destroy');
        });

        Route::middleware(sprintf('can:%s', CmsPermission::Menus->value))->group(function (): void {
            Route::get('menus', [MenuController::class, 'index'])->name('menus.index');
            Route::get('menus/{menu:key}/items/create', [MenuItemController::class, 'create'])->name('menu-items.create');
            Route::post('menus/{menu:key}/items', [MenuItemController::class, 'store'])->name('menu-items.store');
            Route::get('menus/{menu:key}/items/{item}/edit', [MenuItemController::class, 'edit'])->name('menu-items.edit')->scopeBindings();
            Route::put('menus/{menu:key}/items/{item}', [MenuItemController::class, 'update'])->name('menu-items.update')->scopeBindings();
            Route::delete('menus/{menu:key}/items/{item}', [MenuItemController::class, 'destroy'])->name('menu-items.destroy')->scopeBindings();
        });

        Route::middleware(sprintf('can:%s', CmsPermission::Redirects->value))->group(function (): void {
            Route::get('redirects', [RedirectController::class, 'index'])->name('redirects.index');
            Route::get('redirects/create', [RedirectController::class, 'create'])->name('redirects.create');
            Route::post('redirects', [RedirectController::class, 'store'])->name('redirects.store');
            Route::get('redirects/import', [RedirectController::class, 'importForm'])->name('redirects.import');
            Route::post('redirects/import', [RedirectController::class, 'import'])->name('redirects.import.store');
            Route::get('redirects/{redirect}/edit', [RedirectController::class, 'edit'])->name('redirects.edit');
            Route::put('redirects/{redirect}', [RedirectController::class, 'update'])->name('redirects.update');
            Route::delete('redirects/{redirect}', [RedirectController::class, 'destroy'])->name('redirects.destroy');
        });

        Route::middleware(sprintf('can:%s', CmsPermission::Settings->value))->group(function (): void {
            Route::get('settings', [SettingsController::class, 'edit'])->name('settings.edit');
            Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');
        });
    });
