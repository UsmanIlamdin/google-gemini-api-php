<?php

declare(strict_types=1);

namespace GeminiAPI\Resources;

use GeminiAPI\Contracts\Resources\CachedContentsContract;
use GeminiAPI\Data\CachedContent;
use GeminiAPI\Requests\CachedContents\CachedContentRequest;
use GeminiAPI\Requests\CachedContents\ListCachedContentRequest;
use GeminiAPI\Requests\Files\ListFilesRequest;
use GeminiAPI\Responses\CachedContents\ListCachedContentResponse;
use GuzzleHttp\Client;
use Exception;
use GuzzleHttp\Exception\RequestException as GuzzleRequestException;
use Psr\Http\Message\ResponseInterface;

final class CachedContents implements CachedContentsContract
{
    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/';

    public function __construct($api)
    {
        $this->api = $api;
    }

    /**
     * Creates CachedContent resource.
     *
     * @link https://ai.google.dev/api/caching#v1beta.cachedContents.create
     *
     * @param CachedContent $cachedContent Content that has been preprocessed and can be used in subsequent request to GenerativeService.
     *
     * @return CachedContent If successful.
     *
     * @throws RequestException
     */
    public function create(CachedContent $cachedContent): CachedContent
    {
        try {
            $client = new Client();
            $response = $client->request('POST', $this->baseUrl . 'cachedContents?key=' . $this->api, [
                'headers' => [
                    'X-Goog-Upload-Protocol' => 'resumable',
                    'X-Goog-Upload-Command' => 'start',
                ],
                'json' => $cachedContent->toArray(),
            ]);
            $data = json_decode($response->getBody()->getContents(), true);
            return CachedContentRequest::createDtoFromResponse($response);
        } catch (GuzzleRequestException $e) {
            throw new Exception('Failed to initiate upload: ' . $e->getMessage());
        }
    }

    /**
     * Deletes CachedContent resource.
     *
     * @link https://ai.google.dev/api/caching#v1beta.cachedContents.delete
     *
     * @param string $name The resource name referring to the content cache entry.
     *
     * @return bool If successful.
     *
     * @throws RequestException
     */
    public function delete(string $name): bool
    {
        try {
            $client = new Client();
            $response = $client->request('DELETE', $this->baseUrl . $name . '?key=' . $this->api);
            return $response->getStatusCode() === 200;
        } catch (RequestException $e) {
            throw new Exception('Failed to delete cache: ' . $e->getMessage());
        }
    }

    /**
     * Reads CachedContent resource.
     *
     * @link https://ai.google.dev/api/caching#v1beta.cachedContents.get
     *
     * @param string $name The resource name referring to the content cache entry.
     *
     * @return CachedContent If successful.
     *
     * @throws RequestException
     */
    public function get(string $name): CachedContent
    {
        try {
            $client = new Client();
            $response = $client->request('GET', $this->baseUrl . $name . '?key=' . $this->api,);
            $data = json_decode($response->getBody()->getContents(), true);
            return CachedContentRequest::createDtoFromResponse($response);
        } catch (RequestException $e) {
            throw new Exception('Failed to upload file in cache: ' . $e->getMessage());
        }
    }

    /**
     * Lists CachedContents.
     *
     * @link https://ai.google.dev/api/caching#v1beta.cachedContents.list
     *
     * @param array{
     *      pageSize?: int,
     *      pageToken?: string,
     *  } $parameters
     *
     * @return ListCachedContentResponse If successful.
     *
     * @throws RequestException
     */
    public function list(array $parameters = []): ListCachedContentResponse
    {
        try {
            $client = new Client();
            $response = $client->request('GET', $this->baseUrl . 'cachedContents?key=' . $this->api);
            $data = json_decode($response->getBody()->getContents(), true);
            return ListCachedContentRequest::createDtoFromResponse($response);
        } catch (RequestException $e) {
            throw new Exception('Failed to get list of cache: ' . $e->getMessage());
        }
    }

    /**
     * Updates CachedContent resource (only expiration is updatable).
     *
     * @link https://ai.google.dev/api/caching#v1beta.cachedContents.patch
     *
     * @param array{
     *     name: string,
     *     updateMask: string,
     *     cachedContent: CachedContent,
     * } $parameters
     *
     * @return CachedContent If successful.
     *
     * @throws RequestException
     */
    public function patch(array $parameters): CachedContent
    {
        try {
            $client = new Client();
            $response = $client->request('PATCH', $this->baseUrl . $parameters['name'] . '?key=' . $this->api, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => $parameters['cachedContent'],
            ]);
            $data = json_decode($response->getBody()->getContents(), true);
            return CachedContentRequest::createDtoFromResponse($response);
        } catch (RequestException $e) {
            throw new Exception('Failed to get list of cache: ' . $e->getMessage());
        }
    }
}
