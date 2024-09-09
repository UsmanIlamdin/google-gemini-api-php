<?php

namespace GeminiAPI\Requests\CachedContents;

use GeminiAPI\Data\CachedContent;
use Psr\Http\Message\ResponseInterface;

final class CachedContentRequest
{
    /**
     * Creates the DTO from the Guzzle response.
     *
     * @param ResponseInterface $response
     *
     * @return CachedContent
     */
    public static function createDtoFromResponse(ResponseInterface $response): CachedContent
    {
        $data = json_decode((string)$response->getBody(), true);

        return CachedContent::fromArray($data);
    }
}