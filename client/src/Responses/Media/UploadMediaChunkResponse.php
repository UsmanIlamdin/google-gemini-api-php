<?php

namespace GeminiAPI\Responses\Media;

use GeminiAPI\Contracts\ResponseContract;
use GeminiAPI\Data\File;
use InvalidArgumentException;

final class UploadMediaChunkResponse implements ResponseContract
{
    /**
     * @param File $file Metadata for the created file.
     */
    public function __construct(
        public File $file,
    ) {
        //
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['file']) || !is_array($data['file'])) {
            throw new InvalidArgumentException('file is missing or not an array');
        }

        return new self(
            file: File::fromArray($data['file']),
        );
    }

    public function toArray(): array
    {
        return [
            'file' => $this->file->toArray(),
        ];
    }
}
