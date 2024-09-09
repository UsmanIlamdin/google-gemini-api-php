<?php

namespace GeminiAPI\Requests\Media;

use Psr\Http\Message\ResponseInterface;
use GeminiAPI\Responses\Media\UploadMediaChunkResponse;

final class UploadMediaChunkRequest
{
    public static function createDtoFromResponse(ResponseInterface $response): UploadMediaChunkResponse
    {
        $data = json_decode((string) $response->getBody(), true);

        return UploadMediaChunkResponse::fromArray($data);
    }
}