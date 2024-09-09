<?php

declare(strict_types=1);

namespace GeminiAPI\Contracts\Resources;

use GeminiAPI\Data\CachedContent;
use GeminiAPI\Responses\CachedContents\ListCachedContentResponse;
use GuzzleHttp\Exception\RequestException;

interface CachedContentsContract
{
    /**
     * Creates CachedContent resource.
     *
     * @param CachedContent $cachedContent Content that has been preprocessed and can be used in subsequent requests to GenerativeService.
     *
     * @return CachedContent If successful.
     *
     * @throws RequestException
     */
    public function create(CachedContent $cachedContent): CachedContent;

    /**
     * Deletes CachedContent resource.
     *
     * @param string $name The resource name referring to the content cache entry.
     *
     * @return bool If successful.
     *
     * @throws RequestException
     */
    public function delete(string $name): bool;

    /**
     * Reads CachedContent resource.
     *
     * @param string $name The resource name referring to the content cache entry.
     *
     * @return CachedContent If successful.
     *
     * @throws RequestException
     */
    public function get(string $name): CachedContent;

    /**
     * Lists CachedContents.
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
    public function list(array $parameters = []): ListCachedContentResponse;

    /**
     * Updates CachedContent resource (only expiration is updatable).
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
    public function patch(array $parameters): CachedContent;
}