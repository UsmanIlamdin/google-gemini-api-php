<?php

declare(strict_types=1);

namespace GeminiAPI\Resources;

use GeminiAPI\Contracts\Resources\MediaContract;
use GeminiAPI\Data\File;
use GeminiAPI\Requests\Media\UploadMediaChunkRequest;
use GeminiAPI\Responses\Media\UploadMediaChunkResponse;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException as GuzzleRequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\ResponseInterface;

final class Media implements MediaContract
{
    protected const CHUNK_SIZE = 10 * 1024 * 1024;
    protected const UPLOAD_PROTOCOL = 'resumable';
    protected const UPLOAD_COMMAND = 'start';
    private string $baseUrl = 'https://generativelanguage.googleapis.com/upload/v1beta/files';

    public function __construct($api)
    {
        $this->api = $api;
    }

    /**
     * Creates a `File`.
     *
     * @link https://ai.google.dev/api/files#v1beta.media.upload
     *
     * @param string    $filePath     Path to the file to upload
     * @param File|null $fileMetadata Metadata for the file to create.
     *
     * @return UploadMediaChunkResponse If successful.
     *
     * @throws Exception
     */
    public function upload(string $filePath, ?File $fileMetadata = null): UploadMediaChunkResponse
    {
        if (!file_exists($filePath)) {
            throw new Exception('File does not exist. Provide a valid file path');
        }

        $fileSize = filesize($filePath);
        if ($fileSize === false) {
            throw new Exception('Unable to determine file size');
        }

        $mimeType = mime_content_type($filePath);
        if ($mimeType === false) {
            throw new Exception('Unable to determine MIME type');
        }

        $fileMetadata ??= new File();
        $fileMetadata->sizeBytes = (string)$fileSize;
        $fileMetadata->mimeType = $mimeType;
        try {
            $client = new Client();

            $headers = [
                'X-Goog-Upload-Protocol' => self::UPLOAD_PROTOCOL,
                'X-Goog-Upload-Command' => self::UPLOAD_COMMAND,
                'X-Goog-Upload-Header-Content-Length' => $fileMetadata->sizeBytes,
                'X-Goog-Upload-Header-Content-Type' => $mimeType,
                'Content-Type' => 'application/json'
            ];
            $body = json_encode([
                'file' => [
                    'display_name' => $fileMetadata->displayName
                ]
            ]);
            $request = new Request('POST', $this->baseUrl .'?key=' . $this->api, $headers, $body);
            $response = $client->sendAsync($request)->wait();

        } catch (GuzzleRequestException | ConnectException | TransferException $e) {
            throw new Exception('Failed to initiate upload: ' . $e->getMessage());
        }

        $uploadUrl = $response->getHeaderLine('X-Goog-Upload-URL');

        if (!is_string($uploadUrl) || $uploadUrl === '') {
            throw new Exception('Failed to get upload URL from initial request.');
        }

        $handle = fopen($filePath, 'rb');
        if ($handle === false) {
            throw new Exception('Failed to open file for reading');
        }

        $chunkSize = self::CHUNK_SIZE;
        $offset = 0;

        try {
            while (!feof($handle)) {
                $chunkData = fread($handle, $chunkSize);
                if ($chunkData === false) {
                    throw new Exception('Failed to read chunk from file');
                }
                $end = $offset + strlen($chunkData);
                $command = ($end < $fileSize) ? 'upload' : 'upload, finalize';

                try {
                    $client = new Client();
                    $response = $client->post($uploadUrl, [
                        'headers' => [
                            'Content-Length' => strlen($chunkData),
                            'X-Goog-Upload-Command'=>$command,
                            "X-Goog-Upload-Offset" => '0'
                        ],
                        'body' => $chunkData,
                    ]);
                } catch (GuzzleRequestException $e) {
                    $statusCode = $e->getResponse()->getStatusCode();
                    $errorMessage = $e->getMessage();
                    throw new Exception('Chunk upload failed: ' . $e->getMessage(), 0, $e);
                }

                if ($response->getStatusCode() >= 400) {
                    throw new Exception('Chunk upload failed: ' . $response->getReasonPhrase());
                }

                $offset = $end;
            }
        } finally {
            fclose($handle);
        }

        return UploadMediaChunkRequest::createDtoFromResponse($response);
    }
}