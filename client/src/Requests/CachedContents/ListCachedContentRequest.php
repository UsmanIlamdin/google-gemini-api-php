<?php

namespace GeminiAPI\Requests\CachedContents;

use GeminiAPI\Responses\CachedContents\ListCachedContentResponse;
use Psr\Http\Message\ResponseInterface;

final class ListCachedContentRequest
{
    public static function createDtoFromResponse(ResponseInterface $response): ListCachedContentResponse
    {
        $data = json_decode((string) $response->getBody(), true);

        return ListCachedContentResponse::fromArray($data);
    }
}