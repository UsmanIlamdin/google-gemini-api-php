<?php

declare(strict_types=1);

namespace GeminiAPI\Traits\Data;

use GeminiAPI\Data\Blob;
use GeminiAPI\Data\FileData;
use GeminiAPI\Data\Part;
use GeminiAPI\Enums\MimeType;

trait HasPartData
{
    public static function createTextPart(string $text): Part
    {
        return new Part(text: $text);
    }

    public static function createBlobPart(string $mimeType, string $data): Part
    {
        $mimeType = MimeType::from(strtolower($mimeType));

        return new Part(inlineData: new Blob(mimeType: $mimeType, data: $data));
    }

    public static function createFilePart(string $fileUri, ?string $mimeType = null): Part
    {
        $mimeType = isset($mimeType) ? MimeType::from(strtolower($mimeType)) : null;

        return new Part(fileData: new FileData(fileUri: $fileUri, mimeType: $mimeType));
    }

    public static function createTextWithBlobPart(string $text, string $mimeType, string $data): Part
    {
        $mimeType = MimeType::from(strtolower($mimeType));

        return new Part(text: $text, inlineData: new Blob($mimeType, $data));
    }

    public static function createTextWithFilePart(string $text, string $fileUri, ?string $mimeType = null): Part
    {
        $mimeType = isset($mimeType) ? MimeType::from(strtolower($mimeType)) : null;

        return new Part(text: $text, fileData: new FileData(fileUri: $fileUri, mimeType: $mimeType));
    }
}
