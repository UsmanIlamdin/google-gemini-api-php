<?php

declare(strict_types=1);

namespace GeminiAPI\Requests\Files;

use GeminiAPI\Data\File;
use Psr\Http\Message\ResponseInterface;

final class GetFileRequest
{

    public static function createDtoFromResponse(ResponseInterface $response): File
    {
        $data = json_decode((string) $response->getBody(), true);

        return File::fromArray($data);
    }
}
