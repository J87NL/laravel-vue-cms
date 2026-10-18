<?php

declare(strict_types=1);

use Illuminate\Http\UploadedFile;
use J87NL\Cms\CmsPermission;
use J87NL\Cms\Enums\RedirectSource;
use J87NL\Cms\Models\Redirect;

test('the redirects screen needs the redirects permission', function (): void {
    $this->actingWithPermissions([CmsPermission::PagesEdit->value]);

    $this->get(route('cms.redirects.index'))->assertForbidden();
    $this->post(route('cms.redirects.store'), [])->assertForbidden();
});

test('an admin adds, edits and deletes a redirect', function (): void {
    $this->actingWithPermissions([CmsPermission::Redirects->value]);

    $this->get(route('cms.redirects.index'), $this->inertiaHeaders())->assertOk();

    $this->post(route('cms.redirects.store'), [
        'from_path' => '/oud',
        'to' => '/nieuw',
        'status_code' => '302',
    ])->assertRedirect(route('cms.redirects.index'));

    $redirect = Redirect::query()->sole();

    expect($redirect)
        ->from_path->toBe('oud')
        ->status_code->toBe(302)
        ->source->toBe(RedirectSource::Manual);

    $this->put(route('cms.redirects.update', $redirect), [
        'from_path' => '/oud',
        'to' => 'https://example.org',
        'status_code' => '301',
    ])->assertRedirect(route('cms.redirects.index'));

    expect($redirect->fresh()->to)->toBe('https://example.org');

    $this->post(route('cms.redirects.store'), [
        'from_path' => '/oud',
        'to' => '/elders',
        'status_code' => '301',
    ])->assertSessionHasErrors('from_path');

    $this->post(route('cms.redirects.store'), [
        'from_path' => '/x',
        'to' => 'javascript:alert(1)',
        'status_code' => '301',
    ])->assertSessionHasErrors('to');

    $this->delete(route('cms.redirects.destroy', $redirect))->assertRedirect(route('cms.redirects.index'));

    expect(Redirect::query()->count())->toBe(0);
});

test('a csv upload imports redirects and reports failed lines', function (): void {
    $this->actingWithPermissions([CmsPermission::Redirects->value]);

    $this->get(route('cms.redirects.import'), $this->inertiaHeaders())->assertOk();

    $file = UploadedFile::fake()->createWithContent('redirects.csv', "from,to\n/a,/b\n/,/home\n");

    $this->post(route('cms.redirects.import.store'), [
        'file' => $file,
    ])
        ->assertRedirect(route('cms.redirects.index'))
        ->assertSessionHasErrors('import');

    expect(Redirect::query()->pluck('to', 'from_path')->all())->toBe([
        'a' => '/b',
    ]);
});
