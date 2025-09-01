<?php

declare(strict_types=1);

namespace Workbench\App\Uploads;

use Honed\Upload\Upload;

class FileUpload extends Upload
{
    /**
     * {@inheritDoc}
     */
    protected function definition(): static
    {
        return $this
            ->multiple()
            ->maxSize(1024 * 1024 * 10) // 10MB
            ->mime('image/*')
            ->extensions('png', 'jpg', 'jpeg', 'gif', 'svg', 'webp');
    }
}
