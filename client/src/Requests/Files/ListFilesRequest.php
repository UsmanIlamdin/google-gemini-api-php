<?php


declare(strict_types=1);

namespace GeminiAPI\Requests\Files;

use GeminiAPI\Responses\Files\ListFilesResponse;
use Psr\Http\Message\ResponseInterface;

final class ListFilesRequest
{
    public static function createDtoFromResponse(ResponseInterface $response): ListFilesResponse
    {
        $data = json_decode((string) $response->getBody(), true);

        return ListFilesResponse::fromArray($data);
    }
}