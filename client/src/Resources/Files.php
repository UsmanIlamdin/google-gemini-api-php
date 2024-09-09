<?php

declare(strict_types=1);

namespace GeminiAPI\Resources;

use GeminiAPI\Contracts\Resources\FilesContract;
use GeminiAPI\Data\File;
use GeminiAPI\Requests\Files\GetFileRequest;
use GeminiAPI\Requests\Files\ListFilesRequest;
use GeminiAPI\Responses\Files\ListFilesResponse;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\GuzzleException;

final class Files implements FilesContract
{
    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/';

    public function __construct($api)
    {
        $this->api = $api;
    }

    /**
     * Deletes the `File`.
     *
     * @link https://ai.google.dev/api/files#v1beta.files.delete
     *
     * @param string $name The name of the `File` to delete
     *
     * @return bool If successful.
     *
     * @throws GuzzleException
     */
    public function delete(string $name): bool
    {
        try {
            $client = new Client();
            $response = $client->request('DELETE', $this->baseUrl . $name . '?key=' . $this->api);
            return $response->getStatusCode() === 200;
        } catch (RequestException $e) {
            throw new \RuntimeException('Failed to delete file', 0, $e);
        }
    }

    /**
     * Gets the `File`.
     *
     * @link https://ai.google.dev/api/files#v1beta.files.get
     *
     * @param string $name The name of the `File` to get
     *
     * @return File If successful.
     *
     * @throws GuzzleException
     */
    public function get(string $name): File
    {
        try {
            $client = new Client();
            $response = $client->request('GET', $this->baseUrl . $name . '?key=' . $this->api);
            $data = json_decode($response->getBody()->getContents(), true);
            return GetFileRequest::createDtoFromResponse($response);
        } catch (RequestException $e) {
            throw new \RuntimeException('Failed to get file', 0, $e);
        }
    }

    /**
     * Lists the metadata for `File`s owned by the requesting project.
     *
     * @link https://ai.google.dev/api/files#v1beta.files.list
     *
     * @param array{
     *      pageSize?: int,
     *      pageToken?: string,
     * } $parameters
     *
     * @return ListFilesResponse If successful.
     *
     * @throws GuzzleException
     */
    public function list(array $parameters = []): ListFilesResponse
    {
        try {
            $client = new Client();
            $response = $client->request('GET', $this->baseUrl . 'files?key=' . $this->api);
            $data = json_decode($response->getBody()->getContents(), true);
            return ListFilesRequest::createDtoFromResponse($response);
        } catch (RequestException $e) {
            throw new \RuntimeException('Failed to list files', 0, $e);
        }
    }
}