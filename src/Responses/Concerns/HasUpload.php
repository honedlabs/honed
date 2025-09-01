<?php

declare(strict_types=1);

namespace Honed\Honed\Responses\Concerns;

use Honed\Upload\Upload;

/**
 * @phpstan-require-extends \Honed\Honed\Responses\InertiaResponse
 */
trait HasUpload
{
    public const UPLOAD_PROP = 'upload';

    /**
     * The uploader.
     *
     * @var class-string<Upload>|Upload
     */
    protected $upload;

    /**
     * Set the uploader.
     *
     * @param  class-string<Upload>|Upload  $value
     * @return $this
     */
    public function upload(string|Upload $value): static
    {
        $this->upload = $value;

        return $this;
    }

    /**
     * Get the uploader.
     */
    public function getUpload(): ?Upload
    {
        return is_string($this->upload) ? $this->upload::make() : $this->upload;
    }

    /**
     * Determine if there is an uploader.
     */
    public function hasUpload(): bool
    {
        return isset($this->upload);
    }

    /**
     * Determine if there is not an uploader.
     */
    public function missingUpload(): bool
    {
        return ! $this->hasUpload();
    }

    /**
     * Convert the uploader to an array of props.
     *
     * @return array<string, mixed>
     */
    public function hasUploadToProps(): array
    {
        if ($this->hasUpload()) {
            return [
                self::UPLOAD_PROP => $this->getUpload()?->toArray(),
            ];
        }

        return [];
    }
}
