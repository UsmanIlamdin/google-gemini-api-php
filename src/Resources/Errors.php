<?php

declare(strict_types=1);

namespace GeminiAPI\Resources;

use JsonSerializable;

class Errors implements JsonSerializable
{

    /**
     * @param int $code
     * @param string $message
     * @param string $status
     */
    public function __construct(
        public readonly int $code,
        public readonly string $message,
        public readonly string $status,
    ) {}

    /**
     * @param array{
     *     code: int,
     *     message: string,
     *     status: string
     * } $array
     * @return self
     */
    public static function fromArray(array $array): self
    {
        return new self(
            $array['code'],
            $array['message'],
            $array['status']
        );
    }

    /**
     * @return array<string, string|array<string, mixed>>
     */
    public function jsonSerialize(): array
    {
        return [
            'code' => $this->code,
            'message' => $this->message,
            'status' => $this->status,
        ];
    }
}
