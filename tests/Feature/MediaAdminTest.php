<?php

declare(strict_types=1);

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use J87NL\Cms\Actions\UpdateMedia;
use J87NL\Cms\CmsPermission;
use J87NL\Cms\Models\Block;
use J87NL\Cms\Models\MediaAsset;

beforeEach(function (): void {
    Storage::fake('public');

    $this->actingWithPermissions([CmsPermission::Media->value]);
});

test('an image is uploaded with alt text, caption and focal point', function (): void {
    $this->post(route('cms.media.store'), [
        'file' => UploadedFile::fake()->image('photo.jpg', 1200, 800),
        'alt' => 'The beach',
        'caption' => 'Cap Blanc-Nez',
        'focal_x' => 30,
        'focal_y' => 70,
    ])->assertRedirect(route('cms.media.index'));

    $asset = MediaAsset::query()
        ->sole();

    expect($asset->only(['alt', 'caption', 'focal_x', 'focal_y']))->toBe([
        'alt' => 'The beach',
        'caption' => 'Cap Blanc-Nez',
        'focal_x' => 30,
        'focal_y' => 70,
    ])->and($asset->width())->toBe(1200);
});

test('alt text is required and only images are accepted', function (): void {
    $this->post(route('cms.media.store'), [
        'file' => UploadedFile::fake()->create('notes.pdf', 10, 'application/pdf'),
        'alt' => '',
        'focal_x' => 50,
        'focal_y' => 50,
    ])->assertSessionHasErrors(['file', 'alt']);

    expect(MediaAsset::query()
        ->count())->toBe(0);
});

test('an image in use cannot be deleted', function (): void {
    $this->post(route('cms.media.store'), [
        'file' => UploadedFile::fake()->image('photo.jpg', 800, 600),
        'alt' => 'Photo',
        'focal_x' => 50,
        'focal_y' => 50,
    ]);

    $asset = MediaAsset::query()
        ->sole();

    $page = makePage();

    Block::query()
        ->forceCreate([
            'page_id' => $page->id,
            'type' => 'image',
            'data' => [
                'media_id' => $asset->id,
            ],
        ]);

    $this->delete(route('cms.media.destroy', $asset))->assertSessionHasErrors('media');

    expect(MediaAsset::query()
        ->count())->toBe(1);
});

test('formats outside the accepted list are rejected without leaving a row', function (string $name, string $mimeType): void {
    $this->post(route('cms.media.store'), [
        'file' => UploadedFile::fake()->create($name, 20, $mimeType),
        'alt' => 'Photo',
        'focal_x' => 50,
        'focal_y' => 50,
    ])->assertSessionHasErrors('file');

    expect(MediaAsset::query()
        ->count())->toBe(0);
})->with([
    ['photo.heic', 'image/heic'],
    ['photo.bmp', 'image/bmp'],
]);

test('editing an image changes its details and refuses a focal point outside the image', function (): void {
    $this->post(route('cms.media.store'), [
        'file' => UploadedFile::fake()->image('photo.jpg', 1200, 800),
        'alt' => 'The beach',
        'focal_x' => 50,
        'focal_y' => 50,
    ]);

    $asset = MediaAsset::query()
        ->sole();

    $this->put(route('cms.media.update', $asset), [
        'alt' => 'The dunes',
        'caption' => 'Wissant',
        'focal_x' => 101,
        'focal_y' => 40,
    ])->assertSessionHasErrors('focal_x');

    expect($asset->fresh()->alt)->toBe('The beach');

    $this->partialMock(UpdateMedia::class, fn ($mock) => $mock->shouldReceive('handle')
        ->once()
        ->passthru());

    $this->put(route('cms.media.update', $asset), [
        'alt' => 'The dunes',
        'caption' => 'Wissant',
        'focal_x' => 20,
        'focal_y' => 40,
    ])->assertRedirect(route('cms.media.index'));

    expect($asset->fresh()->only(['alt', 'caption', 'focal_x', 'focal_y']))->toBe([
        'alt' => 'The dunes',
        'caption' => 'Wissant',
        'focal_x' => 20,
        'focal_y' => 40,
    ]);
});

test('the upload refuses a file above the configured size', function (): void {
    config()->set('cms.media.max_upload_kilobytes', 50);

    $this->post(route('cms.media.store'), [
        'file' => UploadedFile::fake()->image('photo.jpg', 1200, 800)->size(51),
        'alt' => 'Too big',
        'focal_x' => 50,
        'focal_y' => 50,
    ])->assertSessionHasErrors('file');

    expect(MediaAsset::query()
        ->count())->toBe(0);
});
