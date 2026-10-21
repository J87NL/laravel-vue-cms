<?php

declare(strict_types=1);

return [
    'index' => [
        'title' => 'Media',
        'description' => 'Images for the website. An image can only be deleted once no page uses it.',
    ],
    'create' => [
        'title' => 'Upload image',
        'description' => 'JPEG, PNG, WebP or GIF. Smaller WebP versions are made in the background.',
    ],
    'edit' => [
        'title' => 'Edit image',
    ],
    'sections' => [
        'focal_point' => 'Focal point',
        'replace' => 'Replace file',
    ],
    'fields' => [
        'file' => 'Image',
        'preview' => 'Preview',
        'alt' => 'Alternative text',
        'caption' => 'Caption',
        'focal_x' => 'Horizontal',
        'focal_y' => 'Vertical',
        'usage' => 'Used',
    ],
    'help' => [
        'alt' => 'Describe what the image shows, for visitors who cannot see it.',
        'focal_point' => 'The part of the image that must stay visible when it is cropped, as a percentage from the left and from the top.',
        'replace' => 'Choose a new image. The alt text, caption and focal point stay, and every page that uses this image shows the new file at once.',
    ],
    'actions' => [
        'upload' => 'Upload',
        'save' => 'Save',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'delete_confirm' => 'Delete this image?',
        'replace' => 'Replace file',
        'replace_confirm' => 'Replace the current file? The old file is deleted.',
    ],
    'flash' => [
        'uploaded' => 'Image uploaded.',
        'saved' => 'Image saved.',
        'deleted' => 'Image deleted.',
        'replaced' => 'File replaced.',
    ],
];
