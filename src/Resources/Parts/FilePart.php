<?php

declare(strict_types=1);

namespace GeminiAPI\Resources\Parts;

use GeminiAPI\Enums\MimeType;
use JsonSerializable;

use function json_encode;

class FilePart implements PartInterface, JsonSerializable
{
    public function __construct(
        public readonly string $mimeType,
        public readonly string $fileUri,
    ) {
    }

    /**
     * @return array{
     *     file_data: array{
     *         mime_type: string,
     *         file_uri: string,
     *     },
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'file_data' => [
                'mime_type' => MimeType::from($this->mimeType),
                'file_uri' => $this->fileUri,
            ],
        ];
    }

    public function __toString(): string
    {
        return json_encode($this) ?: '';
    }
}
